<?php

declare(strict_types=1);

namespace Framework\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

class EntrypointFactory
{
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        $globalMiddleware = $container->get(GlobalMiddleware::class);
        assert($globalMiddleware instanceof GlobalMiddleware);

        $router = $container->get('router');
        assert($router instanceof RequestHandlerInterface);

        return new MiddlewareDecoratedHandler($globalMiddleware, $router);
    }
}
