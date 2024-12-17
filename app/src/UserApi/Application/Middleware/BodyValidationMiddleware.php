<?php

declare(strict_types=1);

namespace UserApi\Application\Middleware;

use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UserApi\Application\Model\Error\ValidationError;
use UserApi\Application\Model\Response\ValidationErrorResponse;
use UserApi\Application\Service\DataResponseGenerator;

use function assert;
use function count;
use function is_object;

class BodyValidationMiddleware implements MiddlewareInterface
{
    private ValidatorInterface $validator;

    public function __construct(
        private DataResponseGenerator $dataResponseGenerator,
    ) {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $payload = $request->getParsedBody();
        assert(is_object($payload));

        $validationErrors = $this->validator->validate($payload);
        if (count($validationErrors) === 0) {
            return $handler->handle($request);
        }

        $errorResponses = [];
        foreach ($validationErrors as $validationError) {
            $errorCode = null;

            $validationErrorCode  = $validationError->getCode();
            $validationConstraint = $validationError->getConstraint();
            if ($validationErrorCode !== null && $validationConstraint !== null) {
                $errorCode = $validationConstraint::getErrorName($validationErrorCode);
            }

            $errorResponses[] = new ValidationError(
                $errorCode,
                (string) $validationError->getMessage(),
                $validationError->getPropertyPath(),
            );
        }

        $data = new ValidationErrorResponse($errorResponses);

        $response = $this->dataResponseGenerator->tryGenerate($data, $request);
        if ($response !== null) {
            return $response->withStatus(400);
        }

        return new TextResponse('ERROR [http_400] Validation Error', 400);
    }
}
