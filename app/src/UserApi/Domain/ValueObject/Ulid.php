<?php

declare(strict_types=1);

namespace UserApi\Domain\ValueObject;

use JsonSerializable;
use Stringable;

readonly class Ulid implements Stringable, JsonSerializable
{
    final private function __construct(
        private string $id,
    ) {
    }

    public static function generate(): static
    {
        $id = (string) \Ulid\Ulid::generate();

        return new static($id);
    }

    public static function fromString(string $id): static
    {
        // Assert valid
        \Ulid\Ulid::fromString($id);

        return new static($id);
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function equals(self $other): bool
    {
        return $other::class === static::class
            && $other->id === $this->id;
    }
}
