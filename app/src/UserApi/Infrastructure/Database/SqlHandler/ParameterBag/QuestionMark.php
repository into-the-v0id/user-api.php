<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\SqlHandler\ParameterBag;

final class QuestionMark implements ParameterBag
{
    /** @var list<bool|float|int|string|null> */
    private array $parameters = [];

    public function __construct()
    {
    }

    /** {@inheritDoc} */
    public function add(bool|float|int|string|null $value): string
    {
        $this->parameters[] = $value;

        return '?';
    }

    /** {@inheritDoc} */
    public function getAll(): array
    {
        return $this->parameters;
    }
}
