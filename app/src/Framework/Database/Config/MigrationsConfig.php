<?php

declare(strict_types=1);

namespace Framework\Database\Config;

readonly class MigrationsConfig
{
    public function __construct(
        public string $path,
        public string $namespace,
        public string $tableName = 'migrations',
    ) {
    }
}
