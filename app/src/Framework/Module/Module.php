<?php

declare(strict_types=1);

namespace Framework\Module;

use Framework\ServiceContainer\ContainerConfiguration;
use Psr\Container\ContainerInterface;

interface Module
{
    public function registerServices(ContainerConfiguration $container): void;

    public function bootstrap(ContainerInterface $container): void;
}
