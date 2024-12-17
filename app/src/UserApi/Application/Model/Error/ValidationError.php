<?php

declare(strict_types=1);

namespace UserApi\Application\Model\Error;

final readonly class ValidationError
{
    public function __construct(
        public string|null $code,
        public string $message,
        public string $path,
    ) {
    }
}
