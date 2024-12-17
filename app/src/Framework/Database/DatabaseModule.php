<?php

declare(strict_types=1);

namespace Framework\Database;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command as DoctrineMigrationsCommand;
use Framework\Database\Doctrine\Dbal\ConnectionFactory;
use Framework\Database\Doctrine\Migrations\ConfigurationFactory;
use Framework\Database\Doctrine\Migrations\DependencyFactoryFactory;
use Framework\Database\Pdo\PdoFactory;
use Framework\Module\Module;
use Framework\ServiceContainer\ContainerConfiguration;
use PDO;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class DatabaseModule implements Module
{
    public function registerServices(ContainerConfiguration $container): void
    {
        $container->factory(PDO::class, new PdoFactory());
        $container->factory(Connection::class, new ConnectionFactory());
        $container->factory(Configuration::class, new ConfigurationFactory());
        $container->factory(DependencyFactory::class, new DependencyFactoryFactory());

        $container->configure(
            ConsoleApplication::class,
            static function (ConsoleApplication $console, ContainerInterface $container): void {
                $console->addCommands([
                    $container->get(DoctrineMigrationsCommand\CurrentCommand::class),
                    $container->get(DoctrineMigrationsCommand\DiffCommand::class),
                    $container->get(DoctrineMigrationsCommand\DumpSchemaCommand::class),
                    $container->get(DoctrineMigrationsCommand\ExecuteCommand::class),
                    $container->get(DoctrineMigrationsCommand\GenerateCommand::class),
                    $container->get(DoctrineMigrationsCommand\LatestCommand::class),
                    $container->get(DoctrineMigrationsCommand\ListCommand::class),
                    $container->get(DoctrineMigrationsCommand\MigrateCommand::class),
                    $container->get(DoctrineMigrationsCommand\RollupCommand::class),
                    $container->get(DoctrineMigrationsCommand\StatusCommand::class),
                    $container->get(DoctrineMigrationsCommand\SyncMetadataCommand::class),
                    $container->get(DoctrineMigrationsCommand\UpToDateCommand::class),
                    $container->get(DoctrineMigrationsCommand\VersionCommand::class),
                ]);
            },
        );
    }

    public function bootstrap(ContainerInterface $container): void
    {
    }
}
