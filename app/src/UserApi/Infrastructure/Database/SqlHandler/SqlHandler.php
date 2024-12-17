<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\SqlHandler;

use Throwable;
use UserApi\Infrastructure\Database\SqlHandler\ParameterBag\ParameterBag;
use UserApi\Infrastructure\Exception\DatabaseException;

use function sprintf;

abstract class SqlHandler
{
    abstract public function createParameterBag(): ParameterBag;

    /** @return list<array<string, bool|float|int|string|null>> */
    abstract public function query(string $sql, ParameterBag|null $parameters = null): array;

    /** @return int Count of affected rows */
    abstract public function execute(string $sql, ParameterBag|null $parameters = null): int;

    /** @return int Count of affected rows */
    public function executeWithEffect(string $sql, ParameterBag|null $parameters = null): int
    {
        $affectedRowCount = $this->execute($sql, $parameters);
        if ($affectedRowCount === 0) {
            throw new DatabaseException('Executed SQL without effect');
        }

        return $affectedRowCount;
    }

    /** @return int Count of affected rows */
    public function executeWithExactEffect(
        string $sql,
        ParameterBag|null $parameters,
        int $expectedAffectedRowCount,
    ): int {
        $affectedRowCount = $this->execute($sql, $parameters);
        if ($affectedRowCount !== $expectedAffectedRowCount) {
            throw new DatabaseException(sprintf(
                'Executed SQL with %s affected rows while expecting exactly %s affected rows',
                $affectedRowCount,
                $expectedAffectedRowCount,
            ));
        }

        return $affectedRowCount;
    }

    abstract public function isInTransaction(): bool;

    abstract public function beginTransaction(): void;

    abstract public function commitTransaction(): void;

    abstract public function rollbackTransaction(): void;

    /**
     * @param callable(): T $block
     *
     * @return T
     *
     * @template T
     */
    public function transactional(callable $block)
    {
        $this->beginTransaction();

        try {
            $result = $block();
        } catch (Throwable $e) {
            $this->rollbackTransaction();

            throw $e;
        }

        $this->commitTransaction();

        return $result;
    }
}
