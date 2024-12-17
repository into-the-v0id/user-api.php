<?php

declare(strict_types=1);

namespace Framework\Database\Pdo;

use Framework\Database\Config\DatabaseConfig;
use PDO;
use Psr\Container\ContainerInterface;

use function assert;
use function sprintf;

class PdoFactory
{
    public function __invoke(ContainerInterface $container): PDO
    {
        $databaseConfig = $container->get(DatabaseConfig::class);
        assert($databaseConfig instanceof DatabaseConfig);

        return new PDO(
            sprintf(
                '%s:host=%s;port=%s;dbname=%s',
                $databaseConfig->driver,
                $databaseConfig->hostname,
                $databaseConfig->port,
                $databaseConfig->database,
            ),
            $databaseConfig->username,
            $databaseConfig->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        );
    }
}
