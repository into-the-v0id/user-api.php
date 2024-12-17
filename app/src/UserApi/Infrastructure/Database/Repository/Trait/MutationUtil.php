<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\Repository\Trait;

use UserApi\Infrastructure\Database\SqlHandler\SqlHandler;

use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function sprintf;

trait MutationUtil
{
    abstract protected function getTableName(): string;

    abstract protected function getSqlHandler(): SqlHandler;

    /** @param array<string, bool|float|int|string|null> $data */
    public function createRaw(array $data): void
    {
        $tableName  = $this->getTableName();
        $sqlHandler = $this->getSqlHandler();

        $parameters = $sqlHandler->createParameterBag();
        $sql        = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(', ', array_keys($data)),
            implode(', ', array_map(
                static fn ($value) => $parameters->add($value),
                $data,
            )),
        );

        $sqlHandler->executeWithExactEffect($sql, $parameters, 1);
    }

    /** @param array<string, bool|float|int|string|null> $data */
    public function updateRaw(array $data): void
    {
        $tableName  = $this->getTableName();
        $sqlHandler = $this->getSqlHandler();

        $parameters = $sqlHandler->createParameterBag();
        $sql        = sprintf(
            'UPDATE %s SET %s WHERE %s.id = %s',
            $tableName,
            implode(', ', array_map(
                static fn ($value, $key) => $key . ' = ' . $parameters->add($value),
                array_values($data),
                array_keys($data),
            )),
            $tableName,
            $parameters->add($data['id']),
        );

        $sqlHandler->executeWithExactEffect($sql, $parameters, 1);
    }

    public function deleteRaw(bool|float|int|string|null $id): void
    {
        $tableName  = $this->getTableName();
        $sqlHandler = $this->getSqlHandler();

        $parameters = $sqlHandler->createParameterBag();
        $sql        = sprintf(
            'DELETE FROM %s WHERE %s.id = %s',
            $tableName,
            $tableName,
            $parameters->add($id),
        );

        $sqlHandler->executeWithExactEffect($sql, $parameters, 1);
    }
}
