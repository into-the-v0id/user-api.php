<?php

declare(strict_types=1);

namespace UserApi\Application\Serializer\Normalizer;

use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;
use UserApi\Domain\ValueObject\Ulid;

use function is_string;
use function is_subclass_of;

class UlidNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritDoc}
     *
     * @param mixed[] $context
     */
    public function normalize(mixed $object, string|null $format = null, array $context = []): string
    {
        if (! ($object instanceof Ulid)) {
            throw new InvalidArgumentException('Attempting to serialize something other than a ULID');
        }

        return $object->toString();
    }

    /** {@inheritDoc} */
    public function getSupportedTypes(string|null $format): array
    {
        return ['*' => true];
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed[] $context
     */
    public function supportsNormalization(mixed $data, string|null $format = null, array $context = []): bool
    {
        return $data instanceof Ulid;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed[] $context
     */
    public function denormalize(mixed $data, string $type, string|null $format = null, array $context = []): Ulid
    {
        if ($type !== Ulid::class && ! is_subclass_of($type, Ulid::class)) {
            throw new InvalidArgumentException('Attempting to deserialize to something other than a ULID');
        }

        if (! is_string($data)) {
            $path = $context['deserialization_path'] ?? null;
            if (! is_string($path)) {
                $path = null;
            }

            throw NotNormalizableValueException::createForUnexpectedDataType(
                'The value is not a string',
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $path,
                true,
            );
        }

        try {
            return $type::fromString($data);
        } catch (Throwable $e) {
            $path = $context['deserialization_path'] ?? null;
            if (! is_string($path)) {
                $path = null;
            }

            throw NotNormalizableValueException::createForUnexpectedDataType(
                $e->getMessage(),
                $data,
                [Type::BUILTIN_TYPE_STRING],
                $path,
                false,
                (int) $e->getCode(),
                $e,
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed[] $context
     */
    public function supportsDenormalization(
        mixed $data,
        string $type,
        string|null $format = null,
        array $context = [],
    ): bool {
        return $type === Ulid::class || is_subclass_of($type, Ulid::class);
    }
}
