<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\SqlHandler;

use PDO;
use PDOStatement;
use UserApi\Infrastructure\Database\SqlHandler\ParameterBag\ParameterBag;
use UserApi\Infrastructure\Database\SqlHandler\ParameterBag\QuestionMark as QuestionMarkParameterBag;
use UserApi\Infrastructure\Exception\DatabaseException;

use function assert;
use function is_string;
use function sprintf;

final class PdoSqlHandler extends SqlHandler
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function createParameterBag(): ParameterBag
    {
        return new QuestionMarkParameterBag();
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, ParameterBag|null $parameters = null): array
    {
        $statement = $this->executeStatement($sql, $parameters);

        /** @var list<array<string, bool|float|int|string|null>> $rows */
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function execute(string $sql, ParameterBag|null $parameters = null): int
    {
        $statement = $this->executeStatement($sql, $parameters);

        return $statement->rowCount();
    }

    private function executeStatement(string $sql, ParameterBag|null $parameters = null): PDOStatement
    {
        $preparedStatement = $this->pdo->prepare($sql);
        if ($preparedStatement === false) {
            throw new DatabaseException(
                'PDO error: Failed to create prepared statement for SQL: ' . $sql,
            );
        }

        $success = $preparedStatement->execute($parameters?->getAll() ?? []);
        if (! $success) {
            $errorMessage = $preparedStatement->errorInfo()[2];
            assert(is_string($errorMessage));

            throw new DatabaseException(sprintf(
                'PDO error: [%s] %s',
                $preparedStatement->errorCode(),
                $errorMessage,
            ));
        }

        return $preparedStatement;
    }

    public function isInTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction(): void
    {
        $this->pdo->commit();
    }

    public function rollbackTransaction(): void
    {
        $this->pdo->rollBack();
    }
}
