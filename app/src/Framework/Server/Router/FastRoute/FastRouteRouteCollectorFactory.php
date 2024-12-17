<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Container\ContainerInterface;

class FastRouteRouteCollectorFactory
{
    public function __invoke(ContainerInterface $container): FastRouteRouteCollector
    {
        $configureRoutes = new RouteCollector(new Std(), new GroupCountBased());

        return new FastRouteRouteCollector($configureRoutes);
    }
}
