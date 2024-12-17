<?php

declare(strict_types=1);

namespace UserApi\Factory\Config;

use Framework\Console\Config\ConsoleConfig;

class ConsoleConfigFactory
{
    public function __invoke(): ConsoleConfig
    {
        return new ConsoleConfig(
            name: 'User API',
            version: '0.1',
        );
    }
}
