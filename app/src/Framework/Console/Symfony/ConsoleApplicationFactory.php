<?php

declare(strict_types=1);

namespace Framework\Console\Symfony;

use Framework\Console\Config\ConsoleConfig;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

use function assert;

class ConsoleApplicationFactory
{
    public function __invoke(ContainerInterface $container): ConsoleApplication
    {
        $consoleConfig = $container->get(ConsoleConfig::class);
        assert($consoleConfig instanceof ConsoleConfig);

        $console = new ConsoleApplication(
            $consoleConfig->name,
            $consoleConfig->version,
        );

        if ($consoleConfig->defaultCommand !== null) {
            $console->setDefaultCommand($consoleConfig->defaultCommand);
        }

        return $console;
    }
}
