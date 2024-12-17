<?php

declare(strict_types=1);

namespace Framework\Server;

use Laminas\Stratigility\Next;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

use function array_unshift;

class GlobalMiddleware implements MiddlewareInterface
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function clear(): void
    {
        $this->middlewares = [];
    }

    public function append(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function prepend(MiddlewareInterface $middleware): void
    {
        array_unshift($this->middlewares, $middleware);
    }

    /** @return MiddlewareInterface[] */
    public function getAll(): array
    {
        return $this->middlewares;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $middlewareQueue = new SplQueue();
        foreach ($this->middlewares as $middleware) {
            $middlewareQueue->enqueue($middleware);
        }

        return (new Next($middlewareQueue, $handler))->handle($request);
    }
}
