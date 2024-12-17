<?php

declare(strict_types=1);

namespace Framework\Logger\Config;

readonly class LoggerConfig
{
    public function __construct(
        public string $name,
        public string $level,
        public bool $infoConsole,
        public bool $errorConsole,
        public string|null $infoFile,
        public string|null $errorFile,
    ) {
    }
}
