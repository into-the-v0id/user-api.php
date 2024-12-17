<?php

declare(strict_types=1);

namespace Framework\Console\Config;

readonly class ConsoleConfig
{
    public function __construct(
        public string $name,
        public string $version,
        public string|null $defaultCommand = null,
    ) {
    }
}
