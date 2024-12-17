<?php

declare(strict_types=1);

namespace UserApi\Application\Service;

use Framework\Server\ErrorResponseGenerator\ErrorResponseGenerator as FrameworkErrorResponseGenerator;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UserApi\Application\Model\Error\Error;
use UserApi\Application\Model\Response\ErrorResponse;

use function sprintf;

class ErrorResponseGenerator implements FrameworkErrorResponseGenerator
{
    public function __construct(
        private DataResponseGenerator $dataResponseGenerator,
    ) {
    }

    private static function getHttpStatusReason(int $httpStatusCode): string
    {
        $response = new EmptyResponse(status: $httpStatusCode);

        return $response->getReasonPhrase();
    }

    public function generate(
        int $httpStatusCode,
        ServerRequestInterface $request,
        string|null $code = null,
        string|null $title = null,
        string|null $description = null,
    ): ResponseInterface {
        if ($code === null) {
            $code = 'http_' . $httpStatusCode;
        }

        if ($title === null) {
            $title = self::getHttpStatusReason($httpStatusCode);
        }

        $data = new ErrorResponse(new Error(
            $code,
            $title,
            $description,
        ));

        $response = $this->dataResponseGenerator->tryGenerate($data, $request);
        if ($response !== null) {
            return $response->withStatus($httpStatusCode);
        }

        $body = sprintf(
            'ERROR [%s] %s',
            $code,
            $title,
        );
        if ($description !== null) {
            $body .= ': ' . $description;
        }

        return new TextResponse($body, status: $httpStatusCode);
    }

    public function generateErrorResponse(int $httpStatusCode, ServerRequestInterface $request): ResponseInterface
    {
        return $this->generate($httpStatusCode, $request);
    }
}
