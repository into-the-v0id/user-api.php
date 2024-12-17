<?php

declare(strict_types=1);

namespace Framework\Console;

use Framework\Console\Symfony\ConsoleApplicationFactory;
use Framework\Module\Module;
use Framework\ServiceContainer\ContainerConfiguration;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class ConsoleModule implements Module
{
    public function registerServices(ContainerConfiguration $container): void
    {
        $container->factory(ConsoleApplication::class, new ConsoleApplicationFactory());
    }

    public function bootstrap(ContainerInterface $container): void
    {
    }
}
