<?php

declare(strict_types=1);

namespace UserApi\Domain\ValueObject;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;

use function is_int;
use function microtime;

final readonly class Instant implements Stringable, JsonSerializable
{
    public const DEFAULT_FORMAT = 'Y-m-d\TH:i:s.uP';

    private function __construct(
        private float $timestamp,
    ) {
    }

    public static function now(): self
    {
        $timestamp = microtime(true);

        return new self($timestamp);
    }

    public static function fromTimestamp(float|int $timestamp): self
    {
        if (is_int($timestamp)) {
            $timestamp = (float) $timestamp;
        }

        if ($timestamp < 0) {
            throw new InvalidArgumentException('Timestamp cannot be less than zero');
        }

        return new self($timestamp);
    }

    public static function fromDateTime(DateTimeInterface $dateTime): self
    {
        $timestamp = (float) $dateTime->format('U.u');

        return new self($timestamp);
    }

    public function toTimestamp(): float
    {
        return $this->timestamp;
    }

    public function toDateTime(): DateTimeImmutable
    {
        return DateTimeImmutable::createFromTimestamp($this->timestamp);
    }

    public function format(string $format = self::DEFAULT_FORMAT): string
    {
        return $this->toDateTime()->format($format);
    }

    public function toString(): string
    {
        return $this->format();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): float
    {
        return $this->timestamp;
    }

    public function equals(self $other): bool
    {
        return $this->timestamp === $other->timestamp;
    }
}
