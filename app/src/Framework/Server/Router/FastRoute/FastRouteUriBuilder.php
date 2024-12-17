<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use BackedEnum;
use FastRoute\GenerateUri;
use Framework\Server\Router\UriBuilder;

use function array_map;

class FastRouteUriBuilder implements UriBuilder
{
    public function __construct(
        private GenerateUri $generateUri,
    ) {
    }

    /** {@inheritDoc} */
    public function buildUri(string $name, array $params): string
    {
        $stringParams = array_map(
            static fn ($value) => $value instanceof BackedEnum
                ? (string) $value->value
                : (string) $value,
            $params,
        );

        // Suppress phpstan error
        /** @phpstan-var array<non-empty-string, non-empty-string> $stringParams */

        return $this->generateUri->forRoute($name, $stringParams);
    }
}
