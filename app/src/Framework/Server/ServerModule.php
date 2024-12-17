<?php

declare(strict_types=1);

namespace Framework\Server;

use FastRoute;
use Framework\Module\Module;
use Framework\Server\ErrorHandler\ErrorHandlerMiddleware;
use Framework\Server\ErrorHandler\LoggingErrorSubscriber;
use Framework\Server\ErrorResponseGenerator\ErrorResponseGenerator;
use Framework\Server\ErrorResponseGenerator\TextErrorResponseGenerator;
use Framework\Server\Router\FastRoute\FastRouteDispatcherFactory;
use Framework\Server\Router\FastRoute\FastRouteGenerateUriFactory;
use Framework\Server\Router\FastRoute\FastRouteRouteCollector;
use Framework\Server\Router\FastRoute\FastRouteRouteCollectorFactory;
use Framework\Server\Router\FastRoute\FastRouteRouter;
use Framework\Server\Router\FastRoute\FastRouteUriBuilder;
use Framework\Server\Router\RouteCollector;
use Framework\Server\Router\UriBuilder;
use Framework\ServiceContainer\ContainerConfiguration;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

use function assert;

class ServerModule implements Module
{
    public function registerServices(ContainerConfiguration $container): void
    {
        // Router
        $container->alias(RouteCollector::class, FastRouteRouteCollector::class);
        $container->alias('router', FastRouteRouter::class);
        $container->alias(UriBuilder::class, FastRouteUriBuilder::class);
        $container->factory(FastRouteRouteCollector::class, new FastRouteRouteCollectorFactory());
        $container->factory(FastRoute\Dispatcher::class, new FastRouteDispatcherFactory());
        $container->factory(FastRoute\GenerateUri::class, new FastRouteGenerateUriFactory());

        // Entrypoint
        $container->factory(GlobalMiddleware::class, static fn () => new GlobalMiddleware());
        $container->factory('entrypoint', new EntrypointFactory());

        // PSR Factories
        $container->factory(ResponseFactoryInterface::class, static fn () => new ResponseFactory());
        $container->factory(StreamFactoryInterface::class, static fn () => new StreamFactory());

        // Error Handlers
        $container->alias(ErrorResponseGenerator::class, TextErrorResponseGenerator::class);
        $container->configure(
            ErrorHandlerMiddleware::class,
            static function (ErrorHandlerMiddleware $errorHandlerMiddleware, ContainerInterface $container): void {
                if (! $container->has(LoggerInterface::class)) {
                    return;
                }

                $errorHandlerMiddleware->subscribeToError($container->get(LoggingErrorSubscriber::class));
            },
        );

        // Global Middleware
        $container->configure(
            GlobalMiddleware::class,
            static function (GlobalMiddleware $globalMiddleware, ContainerInterface $container): void {
                $errorHandlerMiddleware = $container->get(ErrorHandlerMiddleware::class);
                assert($errorHandlerMiddleware instanceof ErrorHandlerMiddleware);

                $globalMiddleware->append($errorHandlerMiddleware);
            },
        );
    }

    public function bootstrap(ContainerInterface $container): void
    {
    }
}
