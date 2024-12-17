<?php

declare(strict_types=1);

namespace Framework\Database\Doctrine\Migrations;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Framework\Database\Config\DatabaseConfig;
use Psr\Container\ContainerInterface;

use function assert;

class ConfigurationFactory
{
    public function __invoke(ContainerInterface $container): Configuration
    {
        $databaseConfig = $container->get(DatabaseConfig::class);
        assert($databaseConfig instanceof DatabaseConfig);

        $configuration = new Configuration();

        $configuration->addMigrationsDirectory(
            $databaseConfig->migrations->namespace,
            $databaseConfig->migrations->path,
        );
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(true);

        $storageConfiguration = new TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName($databaseConfig->migrations->tableName);
        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        return $configuration;
    }
}
