<?php

declare(strict_types=1);

namespace Framework\Database\Config;

readonly class DatabaseConfig
{
    public function __construct(
        public string $driver,
        public string $hostname,
        public int $port,
        public string $database,
        public string $username,
        public string|null $password,
        public MigrationsConfig $migrations,
    ) {
    }
}
