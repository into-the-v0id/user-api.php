<?php

declare(strict_types=1);

namespace UserApi\Factory\Config;

use Framework\Logger\Config\LoggerConfig;
use IntoTheVoid\Env\Env;
use Psr\Log\LogLevel;

class LoggerConfigFactory
{
    public function __invoke(): LoggerConfig
    {
        return new LoggerConfig(
            name: 'api',
            level: Env::getString('LOGGER_LEVEL') ?? LogLevel::ERROR,
            infoConsole: true,
            errorConsole: true,
            infoFile: null,
            errorFile: 'data/log/app-error.log',
        );
    }
}
