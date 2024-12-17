<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\SqlHandler;

use UserApi\Infrastructure\Database\SqlHandler\ParameterBag\ParameterBag;

class LazySqlHandler extends SqlHandler
{
    /** @var callable(): SqlHandler */
    private $factory;

    private SqlHandler|null $sqlHandler = null;

    /** @param callable(): SqlHandler $factory */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    private function getSqlHandler(): SqlHandler
    {
        if ($this->sqlHandler === null) {
            $this->sqlHandler = ($this->factory)();
        }

        return $this->sqlHandler;
    }

    public function createParameterBag(): ParameterBag
    {
        return $this->getSqlHandler()->createParameterBag();
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, ParameterBag|null $parameters = null): array
    {
        return $this->getSqlHandler()->query($sql, $parameters);
    }

    public function execute(string $sql, ParameterBag|null $parameters = null): int
    {
        return $this->getSqlHandler()->execute($sql, $parameters);
    }

    public function executeWithEffect(string $sql, ParameterBag|null $parameters = null): int
    {
        return $this->getSqlHandler()->executeWithEffect($sql, $parameters);
    }

    public function executeWithExactEffect(
        string $sql,
        ParameterBag|null $parameters,
        int $expectedAffectedRowCount,
    ): int {
        return $this->getSqlHandler()->executeWithExactEffect($sql, $parameters, $expectedAffectedRowCount);
    }

    public function isInTransaction(): bool
    {
        return $this->getSqlHandler()->isInTransaction();
    }

    public function beginTransaction(): void
    {
        $this->getSqlHandler()->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->getSqlHandler()->commitTransaction();
    }

    public function rollbackTransaction(): void
    {
        $this->getSqlHandler()->rollbackTransaction();
    }

    /** {@inheritDoc} */
    public function transactional(callable $block)
    {
        return $this->getSqlHandler()->transactional($block);
    }
}
