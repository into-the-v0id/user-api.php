<?php

declare(strict_types=1);

namespace Framework\Server\Router;

use Psr\Http\Server\RequestHandlerInterface;

abstract class BaseRouteCollector implements RouteCollector
{
    public function get(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $this->route(['GET'], $path, $handler, $name);
    }

    public function post(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $this->route(['POST'], $path, $handler, $name);
    }

    public function put(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $this->route(['PUT'], $path, $handler, $name);
    }

    public function patch(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $this->route(['PATCH'], $path, $handler, $name);
    }

    public function delete(
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $this->route(['DELETE'], $path, $handler, $name);
    }
}
