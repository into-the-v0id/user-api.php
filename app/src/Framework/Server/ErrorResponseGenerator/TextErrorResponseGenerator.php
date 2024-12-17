<?php

declare(strict_types=1);

namespace Framework\Server\ErrorResponseGenerator;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function sprintf;

class TextErrorResponseGenerator implements ErrorResponseGenerator
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function generateErrorResponse(
        int $httpStatusCode,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($httpStatusCode);

        $response->getBody()->write(sprintf(
            'ERROR %s %s',
            $response->getStatusCode(),
            $response->getReasonPhrase(),
        ));

        return $response->withHeader('Content-Type', 'text/plain; charset=utf-8');
    }
}
