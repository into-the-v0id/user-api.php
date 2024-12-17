<?php

declare(strict_types=1);

namespace Framework\Database\Doctrine\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Framework\Database\Config\DatabaseConfig;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

use function assert;

class ConnectionFactory
{
    public function __invoke(ContainerInterface $container): Connection
    {
        $databaseConfig = $container->get(DatabaseConfig::class);
        assert($databaseConfig instanceof DatabaseConfig);

        return DriverManager::getConnection([
            'driver' => match ($databaseConfig->driver) {
                'mysql' => 'pdo_mysql',
                'sqlite' => 'pdo_sqlite',
                'pgsql' => 'pdo_pgsql',
                'oci' => 'pdo_oci',
                'sqlsrv' => 'pdo_sqlsrv',
                default => throw new InvalidArgumentException('Unknown driver: ' . $databaseConfig->driver),
            },
            'host' => $databaseConfig->hostname,
            'port' => $databaseConfig->port,
            'dbname' => $databaseConfig->database,
            'user' => $databaseConfig->username,
            'password' => $databaseConfig->password ?? '',
        ]);
    }
}
