<?php

declare(strict_types=1);

namespace UserApi\Application\Middleware;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use UserApi\Application\Service\ErrorResponseGenerator;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private ErrorResponseGenerator $errorResponseGenerator,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if ((error_reporting() & $errno) === 0) {
                return false;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->handleError($e, $request);
        } finally {
            restore_error_handler();
        }
    }

    private function handleError(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $this->logger->error($e->getMessage(), [
            'exception' => $this->normalizeException($e),
            'request' => $this->normalizeRequest($request),
        ]);

        return $this->errorResponseGenerator->generate(500, $request);
    }

    /** @return mixed[] */
    private function normalizeException(Throwable $e): array
    {
        $previous = $e->getPrevious();

        return [
            'class' => $e::class,
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'backtrace' => $e->getTraceAsString(),
            'previous' => $previous !== null
                ? $this->normalizeException($previous)
                : null,
        ];
    }

    /** @return mixed[] */
    private function normalizeRequest(ServerRequestInterface $request): array
    {
        return [
            'http_version' => $request->getProtocolVersion(),
            'method' => $request->getMethod(),
            'target' => $request->getRequestTarget(),
            'path' => $request->getUri()->getPath(),
            'query_params' => $request->getQueryParams(),
            'referrer' => $request->getHeaderLine('Referer') ?: null,
        ];
    }
}
