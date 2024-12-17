<?php

declare(strict_types=1);

namespace UserApi\Application\Model\Error;

final readonly class Error
{
    public function __construct(
        public string $code,
        public string $title,
        public string|null $description,
    ) {
    }
}
