<?php

declare(strict_types=1);

namespace Framework\ServiceContainer;

use Psr\Container\ContainerInterface;
use RuntimeException;

use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_merge;

class ServiceContainer implements ContainerInterface, ContainerConfiguration
{
    /** @var array<string, string> */
    protected array $aliases = [];

    /** @var array<string, callable(ContainerInterface, string $id): mixed> */
    protected array $factories = [];

    /** @var array<string, array<callable(mixed $instance, ContainerInterface, string $id): mixed>> */
    protected array $decorators = [];

    /** @var array<string, bool> */
    protected array $isShared = [];

    /** @var array<string, mixed> */
    protected array $sharedInstances = [];

    public function __construct(
        protected ContainerInterface|null $fallbackContainer = null,
    ) {
    }

    public function getFallbackContainer(): ContainerInterface|null
    {
        return $this->fallbackContainer;
    }

    public function setFallbackContainer(ContainerInterface|null $container): void
    {
        $this->fallbackContainer = $container;
    }

    /** {@inheritDoc} */
    public function factory(string $id, callable $factory, bool $isShared = true): void
    {
        $this->factories[$id] = $factory;
        $this->isShared[$id]  = $isShared;
        unset($this->aliases[$id], $this->sharedInstances[$id]);
    }

    public function alias(string $aliasId, string $targetId): void
    {
        $this->aliases[$aliasId] = $targetId;
        unset($this->factories[$aliasId], $this->isShared[$aliasId], $this->sharedInstances[$aliasId]);
    }

    /** {@inheritDoc} */
    public function decorate(string $id, callable $decorator): void
    {
        if (! isset($this->decorators[$id])) {
            $this->decorators[$id] = [];
        }

        $this->decorators[$id][] = $decorator;
        unset($this->sharedInstances[$id]);
    }

    /** {@inheritDoc} */
    public function configure(string $id, callable $configurator): void
    {
        $this->decorate(
            $id,
            static function ($instance, ContainerInterface $container) use ($configurator, $id): mixed {
                $configurator($instance, $container, $id);

                return $instance;
            },
        );
    }

    public function has(string $id): bool
    {
        if (isset($this->aliases[$id])) {
            return $this->has($this->aliases[$id]);
        }

        $isShared = $this->isShared[$id] ?? true;
        if ($isShared && array_key_exists($id, $this->sharedInstances)) {
            return true;
        }

        if (isset($this->factories[$id])) {
            return true;
        }

        if ($this->fallbackContainer !== null) {
            return $this->fallbackContainer->has($id);
        }

        return false;
    }

    public function get(string $id): mixed
    {
        if (isset($this->aliases[$id])) {
            return $this->get($this->aliases[$id]);
        }

        $isShared = $this->isShared[$id] ?? true;
        if ($isShared && array_key_exists($id, $this->sharedInstances)) {
            return $this->sharedInstances[$id];
        }

        if (isset($this->factories[$id])) {
            $factory = $this->factories[$id];
            $service = $factory($this, $id);
        } elseif ($this->fallbackContainer !== null) {
            $service = $this->fallbackContainer->get($id);
        } else {
            throw new RuntimeException('err'); // TODO
        }

        $decorators = $this->getDecoratorsForIdRecursive($id);
        foreach ($decorators as $decorator) {
            $service = $decorator($service, $this, $id);
        }

        if ($isShared) {
            $this->sharedInstances[$id] = $service;
        }

        return $service;
    }

    /**
     * @param array<callable(mixed $instance, ContainerInterface, string $id): mixed> $baseDecorators
     *
     * @return array<callable(mixed $instance, ContainerInterface, string $id): mixed>
     */
    private function getDecoratorsForIdRecursive(string $id, array $baseDecorators = []): array
    {
        $decorators = array_merge(
            $baseDecorators,
            $this->decorators[$id] ?? [],
        );

        $aliasIds = $this->getAliasIdsForTargetId($id);
        foreach ($aliasIds as $aliasId) {
            $decorators = array_merge(
                $decorators,
                $this->getDecoratorsForIdRecursive($aliasId, $decorators),
            );
        }

        return $decorators;
    }

    /** @return string[] */
    private function getAliasIdsForTargetId(string $targetId): array
    {
        return array_keys(array_filter(
            $this->aliases,
            static fn (string $aliasTargetId) => $aliasTargetId === $targetId,
        ));
    }
}
