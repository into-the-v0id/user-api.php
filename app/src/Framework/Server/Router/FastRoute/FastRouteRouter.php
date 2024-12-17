<?php

declare(strict_types=1);

namespace Framework\Server\Router\FastRoute;

use FastRoute\Dispatcher;
use FastRoute\Dispatcher\Result\Matched;
use FastRoute\Dispatcher\Result\MethodNotAllowed;
use FastRoute\Dispatcher\Result\NotMatched;
use Framework\Server\ErrorResponseGenerator\ErrorResponseGenerator;
use LogicException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_map;
use function assert;
use function get_debug_type;
use function implode;
use function in_array;
use function strtoupper;

class FastRouteRouter implements RequestHandlerInterface
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ErrorResponseGenerator $errorResponseGenerator,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestUri = $request->getUri()->getPath();
        if ($requestUri === '') {
            $requestUri = '/';
        }

        $result = $this->dispatcher->dispatch(
            $request->getMethod(),
            $requestUri,
        );

        if ($result instanceof Matched) {
            $handler = $result->handler;
            assert($handler instanceof RequestHandlerInterface);

            $params = $result->variables;

            foreach ($params as $paramName => $paramValue) {
                $request = $request->withAttribute($paramName, $paramValue);
            }

            $response = $handler->handle($request);

            if (strtoupper($request->getMethod()) === 'HEAD') {
                $response = $response->withBody($this->streamFactory->createStream());
            }

            return $response;
        }

        if ($result instanceof MethodNotAllowed) {
            $allowedMethods = array_map(
                static fn (string $method) => strtoupper($method),
                $result->allowedMethods,
            );

            if (
                ! in_array('HEAD', $allowedMethods, true)
                && in_array('GET', $allowedMethods, true)
            ) {
                $allowedMethods[] = 'HEAD';
            }

            if (! in_array('OPTIONS', $allowedMethods, true)) {
                $allowedMethods[] = 'OPTIONS';
            }

            if (strtoupper($request->getMethod()) === 'OPTIONS') {
                return $this->responseFactory->createResponse(204)
                    ->withHeader('Allow', implode(',', $allowedMethods));
            }

            $response = $this->errorResponseGenerator->generateErrorResponse(405, $request)
                ->withHeader('Allow', implode(',', $allowedMethods));

            if (strtoupper($request->getMethod()) === 'HEAD') {
                $response = $response->withBody($this->streamFactory->createStream());
            }

            return $response;
        }

        if ($result instanceof NotMatched) {
            if (strtoupper($request->getMethod()) === 'OPTIONS') {
                return $this->responseFactory->createResponse(404);
            }

            $response = $this->errorResponseGenerator->generateErrorResponse(404, $request);

            if (strtoupper($request->getMethod()) === 'HEAD') {
                $response = $response->withBody($this->streamFactory->createStream());
            }

            return $response;
        }

        // @phpstan-ignore deadCode.unreachable
        throw new LogicException('Unexpected fast route result: ' . get_debug_type($result));
    }
}
