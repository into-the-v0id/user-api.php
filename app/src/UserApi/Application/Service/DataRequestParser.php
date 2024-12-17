<?php

declare(strict_types=1);

namespace UserApi\Application\Service;

use Nette\Utils\Arrays;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use UserApi\Application\Exception\RequestParser\InvalidSyntax;
use UserApi\Application\Exception\RequestParser\SchemaViolation;
use UserApi\Application\Exception\RequestParser\UnsupportedMediaType;

use function assert;
use function explode;
use function implode;
use function is_string;
use function sprintf;

class DataRequestParser
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws SchemaViolation
     * @throws InvalidSyntax
     * @throws UnsupportedMediaType
     *
     * @template T of object
     */
    public function parse(string $className, ServerRequestInterface $request)
    {
        $contentTypeHeader = $request->getHeaderLine('Content-Type');
        $mediaType         = explode(';', $contentTypeHeader)[0] ?? '';
        $payload           = (string) $request->getBody();

        return match ($mediaType) {
            'application/json' => $this->parseJson($className, $payload),
            default => throw new UnsupportedMediaType(
                sprintf(
                    'Unsupported media type "%s"',
                    $contentTypeHeader,
                ),
            ),
        };
    }

    /**
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws SchemaViolation
     * @throws InvalidSyntax
     *
     * @template T of object
     */
    private function parseJson(string $className, string $payload)
    {
        try {
            return $this->serializer->deserialize(
                $payload,
                $className,
                'json',
                [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false],
            );
        } catch (MissingConstructorArgumentsException $e) {
            $propertyName = Arrays::first($e->getMissingConstructorArguments());

            throw new SchemaViolation(
                sprintf(
                    'Missing property "%s"',
                    $propertyName,
                ),
                previous: $e,
            );
        } catch (ExtraAttributesException $e) {
            $propertyName = Arrays::first($e->getExtraAttributes());
            assert(is_string($propertyName));

            throw new SchemaViolation(
                sprintf(
                    'Unknown property "%s"',
                    $propertyName,
                ),
                previous: $e,
            );
        } catch (NotNormalizableValueException $e) {
            throw new SchemaViolation(
                sprintf(
                    'Type mismatch: Expected "%s" got "%s" at "%s"',
                    implode('|', $e->getExpectedTypes()),
                    $e->getCurrentType(),
                    $e->getPath(),
                ),
                previous: $e,
            );
        } catch (NotEncodableValueException $e) {
            throw new InvalidSyntax(
                'Syntax Error',
                previous: $e,
            );
        }
    }
}
