<?php

declare(strict_types=1);

namespace Framework\Server\ErrorHandler;

use ErrorException;
use Framework\Server\ErrorResponseGenerator\ErrorResponseGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    /** @var array<callable(Throwable, ServerRequestInterface): void> */
    private array $errorSubscribers = [];

    public function __construct(
        private ErrorResponseGenerator $errorResponseGenerator,
    ) {
    }

    /** @param callable(Throwable, ServerRequestInterface): void $subscriber */
    public function subscribeToError(callable $subscriber): void
    {
        $this->errorSubscribers[] = $subscriber;
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
            foreach ($this->errorSubscribers as $subscriber) {
                $subscriber($e, $request);
            }

            return $this->errorResponseGenerator->generateErrorResponse(500, $request);
        } finally {
            restore_error_handler();
        }
    }
}
