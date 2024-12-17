<?php

declare(strict_types=1);

namespace UserApi\Application\Service;

use Laminas\Diactoros\Response\TextResponse;
use Negotiation\Accept;
use Negotiation\Negotiator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function assert;

class DataResponseGenerator
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    public function tryGenerate(mixed $data, ServerRequestInterface $request): ResponseInterface|null
    {
        $acceptHeader        = $request->getHeaderLine('Accept');
        $supportedMediaTypes = ['application/json'];

        $match = (new Negotiator())
            ->getBest($acceptHeader, $supportedMediaTypes);
        if ($match === null) {
            return null;
        }

        assert($match instanceof Accept);
        $mediaType = $match->getType();

        return match ($mediaType) {
            'application/json' => $this->generateJson($data),
            default => null,
        };
    }

    private function generateJson(mixed $data): ResponseInterface
    {
        $encodedData = $this->serializer->serialize($data, 'json');

        $response = new TextResponse($encodedData);
        $response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }

    public function generate(mixed $data, ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->tryGenerate($data, $request);
        if ($response === null) {
            return new TextResponse('ERROR [http_406] Not Acceptable', 406);
        }

        return $response;
    }
}
