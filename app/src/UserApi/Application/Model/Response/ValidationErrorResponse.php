<?php

declare(strict_types=1);

namespace UserApi\Application\Model\Response;

use UserApi\Application\Model\Error\ValidationError;

final readonly class ValidationErrorResponse
{
    /** @param ValidationError[] $errors */
    public function __construct(
        public array $errors,
    ) {
    }
}
