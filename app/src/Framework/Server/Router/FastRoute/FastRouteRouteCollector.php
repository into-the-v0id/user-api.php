<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use FastRoute\ConfigureRoutes;
use Framework\Server\MiddlewareDecoratedHandler;
use Framework\Server\MiddlewareStack;
use Framework\Server\Router\BaseRouteCollector;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function ltrim;
use function rtrim;

class FastRouteRouteCollector extends BaseRouteCollector
{
    private string|null $pathGroup                    = null;
    private MiddlewareInterface|null $middlewareGroup = null;

    public function __construct(
        private ConfigureRoutes $configureRoutes,
    ) {
    }

    public function getConfigureRoutes(): ConfigureRoutes
    {
        return $this->configureRoutes;
    }

    /** {@inheritDoc} */
    public function route(
        array $methods,
        string $path,
        RequestHandlerInterface $handler,
        string|null $name = null,
    ): void {
        $groupedPath = rtrim($this->pathGroup ?? '', '/') . '/' . ltrim($path, '/');

        if ($this->middlewareGroup !== null) {
            $groupedHandler = new MiddlewareDecoratedHandler($this->middlewareGroup, $handler);
        } else {
            $groupedHandler = $handler;
        }

        $extraParameters = [];
        if ($name !== null) {
            $extraParameters[ConfigureRoutes::ROUTE_NAME] = $name;
        }

        $this->configureRoutes->addRoute($methods, $groupedPath, $groupedHandler, $extraParameters);
    }

    public function any(string $path, RequestHandlerInterface $handler, string|null $name = null): void
    {
        $groupedPath = rtrim($this->pathGroup ?? '', '/') . '/' . ltrim($path, '/');

        if ($this->middlewareGroup !== null) {
            $groupedHandler = new MiddlewareDecoratedHandler($this->middlewareGroup, $handler);
        } else {
            $groupedHandler = $handler;
        }

        $extraParameters = [];
        if ($name !== null) {
            $extraParameters[ConfigureRoutes::ROUTE_NAME] = $name;
        }

        $this->configureRoutes->any($groupedPath, $groupedHandler, $extraParameters);
    }

    public function groupPath(string $path, callable $inner): void
    {
        $new            = clone $this;
        $new->pathGroup = rtrim($new->pathGroup ?? '', '/') . '/' . ltrim($path, '/');

        $inner($new);
    }

    public function groupMiddleware(MiddlewareInterface $middleware, callable $inner): void
    {
        $new = clone $this;

        if ($new->middlewareGroup === null) {
            $new->middlewareGroup = $middleware;
        } else {
            $new->middlewareGroup = new MiddlewareStack([
                $new->middlewareGroup,
                $middleware,
            ]);
        }

        $inner($new);
    }
}
