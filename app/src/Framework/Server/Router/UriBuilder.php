<?php

declare(strict_types=1);

namespace Framework\Server\Router;

use BackedEnum;
use Stringable;

interface UriBuilder
{
    /** @param array<string, string|Stringable|int|float|BackedEnum> $params */
    public function buildUri(string $name, array $params): string;
}
