<?php

declare(strict_types=1);

namespace Framework\Server\ErrorHandler;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class LoggingErrorSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Throwable $exception, ServerRequestInterface $request): void
    {
        $this->logger->error($exception->getMessage(), [
            'exception' => $this->normalizeException($exception),
            'request' => $this->normalizeRequest($request),
        ]);
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
