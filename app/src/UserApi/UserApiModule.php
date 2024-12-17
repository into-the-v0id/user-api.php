<?php

declare(strict_types=1);

namespace UserApi;

use Framework\Console\Config\ConsoleConfig;
use Framework\Database\Config\DatabaseConfig;
use Framework\Logger\Config\LoggerConfig;
use Framework\Module\Module;
use Framework\Server\ErrorResponseGenerator\ErrorResponseGenerator as FrameworkErrorResponseGenerator;
use Framework\Server\LazyHandler;
use Framework\Server\MiddlewareDecoratedHandler;
use Framework\Server\MiddlewareStack;
use Framework\Server\Router\RouteCollector;
use Framework\ServiceContainer\ContainerConfiguration;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Serializer\SerializerInterface;
use UserApi\Application\Command;
use UserApi\Application\Handler;
use UserApi\Application\Middleware;
use UserApi\Application\Payload;
use UserApi\Application\Service\ErrorResponseGenerator;
use UserApi\Domain\Repository as RepositoryInterface;
use UserApi\Factory\Config\ConsoleConfigFactory;
use UserApi\Factory\Config\DatabaseConfigFactory;
use UserApi\Factory\Config\LoggerConfigFactory;
use UserApi\Factory\Infrastructure\Serializer\SerializerFactory;
use UserApi\Infrastructure\Database\Repository as DatabaseRepository;
use UserApi\Infrastructure\Database\SqlHandler\LazySqlHandler;
use UserApi\Infrastructure\Database\SqlHandler\PdoSqlHandler;
use UserApi\Infrastructure\Database\SqlHandler\SqlHandler;

class UserApiModule implements Module
{
    public function registerServices(ContainerConfiguration $container): void
    {
        // Config
        $container->factory(LoggerConfig::class, new LoggerConfigFactory());
        $container->factory(DatabaseConfig::class, new DatabaseConfigFactory());
        $container->factory(ConsoleConfig::class, new ConsoleConfigFactory());

        // Serializer
        $container->factory(SerializerInterface::class, new SerializerFactory());

        // Database
        $container->factory(
            SqlHandler::class,
            static fn (ContainerInterface $container) => new LazySqlHandler(
                static fn () => $container->get(PdoSqlHandler::class),
            ),
        );

        // Repositories
        $container->alias(
            RepositoryInterface\UserRepository::class,
            DatabaseRepository\UserRepository::class,
        );

        // Error Response Generator
        $container->alias(
            FrameworkErrorResponseGenerator::class,
            ErrorResponseGenerator::class,
        );

        // Route Collector
        $container->configure(
            RouteCollector::class,
            function (RouteCollector $routeCollector, ContainerInterface $container): void {
                $this->registerRoutes($routeCollector, $container);
            },
        );

        // Console
        $container->configure(
            ConsoleApplication::class,
            static function (ConsoleApplication $console, ContainerInterface $container): void {
                $console->addCommands([
                    $container->get(Command\UserCreateCommand::class),
                ]);
            },
        );
    }

    public function bootstrap(ContainerInterface $container): void
    {
    }

    private function registerRoutes(RouteCollector $routes, ContainerInterface $container): void
    {
        $routes->groupPath('/users', static function (RouteCollector $userRoutes) use ($container): void {
            $userRoutes->get(
                '/',
                new LazyHandler(static fn () => $container->get(Handler\User\UserListHandler::class)),
                'user.list',
            );

            $userRoutes->post(
                '/',
                new LazyHandler(static fn () => new MiddlewareDecoratedHandler(new MiddlewareStack([
                    $container->get(Middleware\BodyParserMiddleware::class)
                        ->withTargetType(Payload\User\CreateUser::class),
                    $container->get(Middleware\BodyValidationMiddleware::class),
                ]), $container->get(Handler\User\UserCreateHandler::class))),
                'user.create',
            );

            $userRoutes->get(
                '/{userId}',
                new LazyHandler(static fn () => new MiddlewareDecoratedHandler(new MiddlewareStack([
                    $container->get(Middleware\User\Load::class),
                ]), $container->get(Handler\User\UserDetailHandler::class))),
                'user.detail',
            );

            $userRoutes->put(
                '/{userId}',
                new LazyHandler(static fn () => new MiddlewareDecoratedHandler(new MiddlewareStack([
                    $container->get(Middleware\User\Load::class),
                    $container->get(Middleware\BodyParserMiddleware::class)
                        ->withTargetType(Payload\User\UpdateUser::class),
                    $container->get(Middleware\BodyValidationMiddleware::class),
                ]), $container->get(Handler\User\UserUpdateHandler::class))),
                'user.update',
            );

            $userRoutes->delete(
                '/{userId}',
                new LazyHandler(static fn () => new MiddlewareDecoratedHandler(new MiddlewareStack([
                    $container->get(Middleware\User\Load::class),
                ]), $container->get(Handler\User\UserDeleteHandler::class))),
                'user.delete',
            );
        });
    }
}
