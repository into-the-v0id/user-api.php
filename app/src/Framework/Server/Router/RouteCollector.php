<?php

declare(strict_types=1);

namespace Framework\Server\Router;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface RouteCollector
{
    /** @param string[] $methods */
    public function route(
        array $methods,
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function get(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function post(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function put(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function patch(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function delete(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    public function any(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void;

    /** @param callable(static): void $inner */
    public function groupPath(
        string $path,
        callable $inner,
    ): void;

    /** @param callable(static): void $inner */
    public function groupMiddleware(
        MiddlewareInterface $middleware,
        callable $inner,
    ): void;
}
