<?php

declare(strict_types=1);

namespace Framework\ServiceContainer;

use Framework\ServiceContainer\Exception\ServiceNotCreateable;
use Invoker\Invoker;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

use function array_diff;
use function array_keys;
use function array_map;
use function class_exists;
use function count;
use function implode;
use function ksort;
use function sprintf;

class InvokerContainer implements ContainerInterface
{
    public function __construct(
        protected Invoker $invoker,
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
    public function has(string $id): bool
    {
        if (class_exists($id)) {
            return true;
        }

        if ($this->fallbackContainer !== null) {
            return $this->fallbackContainer->has($id);
        }

        return false;
    }

    /** {@inheritDoc} */
    public function get(string $id): mixed
    {
        if (! class_exists($id)) {
            if ($this->fallbackContainer !== null) {
                return $this->fallbackContainer->get($id);
            }

            throw new ServiceNotCreateable(sprintf(
                'Unable to create service with id %s because no such class exists',
                $id,
            ), serviceId: $id);
        }

        $reflection = new ReflectionClass($id);
        if (! $reflection->isInstantiable()) {
            if ($this->fallbackContainer !== null) {
                return $this->fallbackContainer->get($id);
            }

            throw new ServiceNotCreateable(sprintf(
                'Unable to create service with id %s because the class it is not instantiatable',
                $id,
            ), serviceId: $id);
        }

        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $resolvedParameters = $this->invoker->getParameterResolver()->getParameters($constructor, [], []);
        ksort($resolvedParameters);

        $reflectionParameters = $constructor->getParameters();

        if (count($resolvedParameters) !== count($reflectionParameters)) {
            $missingParameterIndexes = array_diff(
                array_keys($reflectionParameters),
                array_keys($resolvedParameters),
            );

            $missingReflectionParameters = array_map(
                static fn (int $index) => $reflectionParameters[$index],
                $missingParameterIndexes,
            );

            throw new ServiceNotCreateable(sprintf(
                'Unable to create service with id %s because the following constructor parameters could not '
                    . 'be resolved: %s',
                $id,
                implode(', ', array_map(
                    static fn (ReflectionParameter $parameter) => $parameter->getType() . ' $' . $parameter->getName(),
                    $missingReflectionParameters,
                )),
            ), serviceId: $id);
        }

        return $reflection->newInstance(...$resolvedParameters);
    }
}
