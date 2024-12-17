<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use FastRoute\Dispatcher\GroupCountBased;
use Psr\Container\ContainerInterface;

use function assert;

class FastRouteDispatcherFactory
{
    public function __invoke(ContainerInterface $container): GroupCountBased
    {
        $routeCollector = $container->get(FastRouteRouteCollector::class);
        assert($routeCollector instanceof FastRouteRouteCollector);

        $data = $routeCollector->getConfigureRoutes()->processedRoutes();

        return new GroupCountBased($data);
    }
}
