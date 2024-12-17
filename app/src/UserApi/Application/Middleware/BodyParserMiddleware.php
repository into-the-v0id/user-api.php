<?php

declare(strict_types=1);

namespace UserApi\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use UserApi\Application\Exception\RequestParser\InvalidSyntax;
use UserApi\Application\Exception\RequestParser\SchemaViolation;
use UserApi\Application\Exception\RequestParser\UnsupportedMediaType;
use UserApi\Application\Service\DataRequestParser;
use UserApi\Application\Service\ErrorResponseGenerator;

class BodyParserMiddleware implements MiddlewareInterface
{
    /** @param class-string<object>|null $targetType */
    public function __construct(
        private DataRequestParser $dataRequestParser,
        private ErrorResponseGenerator $errorResponseGenerator,
        private string|null $targetType = null,
    ) {
    }

    /**
     * @param class-string<object> $targetType
     *
     * @return $this
     */
    public function withTargetType(string $targetType): self
    {
        $clone             = clone $this;
        $clone->targetType = $targetType;

        return $clone;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->targetType === null) {
            throw new RuntimeException('Missing target type');
        }

        try {
            $data = $this->dataRequestParser->parse($this->targetType, $request);
        } catch (SchemaViolation) {
            return $this->errorResponseGenerator->generate(400, $request, description: 'Schema violation');
        } catch (InvalidSyntax) {
            return $this->errorResponseGenerator->generate(400, $request, description: 'Syntax Error');
        } catch (UnsupportedMediaType) {
            return $this->errorResponseGenerator->generate(415, $request);
        }

        $request = $request->withParsedBody($data);

        return $handler->handle($request);
    }
}
