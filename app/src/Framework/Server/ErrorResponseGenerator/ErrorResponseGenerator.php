<?php

declare(strict_types=1);

namespace Framework\Server\ErrorResponseGenerator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ErrorResponseGenerator
{
    public function generateErrorResponse(
        int $httpStatusCode,
        ServerRequestInterface $request,
    ): ResponseInterface;
}
