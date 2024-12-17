<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\SqlHandler\ParameterBag;

interface ParameterBag
{
    public function add(bool|float|int|string|null $value): string;

    /**
     * @return array<bool|float|int|string|null>
     *
     * @psalm-pure
     */
    public function getAll(): array;
}
