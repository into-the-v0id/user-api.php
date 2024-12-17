<?php

declare(strict_types=1);

namespace UserApi\Application\Model\Response;

use UserApi\Application\Model\Error\Error;

final readonly class ErrorResponse
{
    public function __construct(
        public Error $error,
    ) {
    }
}
