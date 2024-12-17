<?php

declare(strict_types=1);

namespace Framework\Logger;

use Framework\Logger\Monolog\LoggerFactory;
use Framework\Module\Module;
use Framework\ServiceContainer\ContainerConfiguration;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggerModule implements Module
{
    public function registerServices(ContainerConfiguration $container): void
    {
        $container->factory(LoggerInterface::class, new LoggerFactory());
    }

    public function bootstrap(ContainerInterface $container): void
    {
    }
}
