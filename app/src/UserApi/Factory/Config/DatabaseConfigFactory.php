<?php

declare(strict_types=1);

namespace UserApi\Factory\Config;

use Framework\Database\Config\DatabaseConfig;
use Framework\Database\Config\MigrationsConfig;
use IntoTheVoid\Env\Env;

class DatabaseConfigFactory
{
    public function __invoke(): DatabaseConfig
    {
        return new DatabaseConfig(
            driver: Env::getString('DB_DRIVER') ?? 'pgsql',
            hostname: Env::getString('DB_HOSTNAME') ?? 'localhost',
            port: Env::getInt('DB_PORT') ?? 5432,
            database: Env::getRequiredString('DB_DATABASE'),
            username: Env::getRequiredString('DB_USERNAME'),
            password: Env::getString('DB_PASSWORD'),
            migrations: new MigrationsConfig(
                path: 'src/UserApi/Infrastructure/Doctrine/Migrations/Migrations',
                namespace: 'UserApi\Infrastructure\Doctrine\Migrations\Migrations',
            ),
        );
    }
}
