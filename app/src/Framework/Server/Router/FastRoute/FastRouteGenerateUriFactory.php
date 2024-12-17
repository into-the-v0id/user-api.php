<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use FastRoute\GenerateUri\FromProcessedConfiguration;
use Psr\Container\ContainerInterface;

use function assert;

class FastRouteGenerateUriFactory
{
    public function __invoke(ContainerInterface $container): FromProcessedConfiguration
    {
        $routeCollector = $container->get(FastRouteRouteCollector::class);
        assert($routeCollector instanceof FastRouteRouteCollector);

        $data = $routeCollector->getConfigureRoutes()->processedRoutes()[2];

        return new FromProcessedConfiguration($data);
    }
}
