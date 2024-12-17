<?php

declare(strict_types=1);

namespace Framework\Database\Doctrine\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class DependencyFactoryFactory
{
    public function __invoke(ContainerInterface $container): DependencyFactory
    {
        $logger = null;
        if ($container->has(LoggerInterface::class)) {
            $logger = $container->get(LoggerInterface::class);
        }

        return DependencyFactory::fromConnection(
            new ExistingConfiguration($container->get(Configuration::class)),
            new ExistingConnection($container->get(Connection::class)),
            $logger,
        );
    }
}
