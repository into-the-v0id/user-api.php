<?php

declare(strict_types=1);

namespace Framework\ServiceContainer;

use Psr\Container\ContainerInterface;

interface ContainerConfiguration
{
    /** @param callable(ContainerInterface, string $id): mixed $factory */
    public function factory(
        string $id,
        callable $factory,
        bool $isShared = true,
    ): void;

    public function alias(
        string $aliasId,
        string $targetId,
    ): void;

    /** @param callable(mixed $instance, ContainerInterface, string $id): mixed $decorator */
    public function decorate(
        string $id,
        callable $decorator,
    ): void;

    /** @param callable(mixed $instance, ContainerInterface, string $id): void $configurator */
    public function configure(
        string $id,
        callable $configurator,
    ): void;
}
