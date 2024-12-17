<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\Repository\Trait;

use DateTimeImmutable;
use UserApi\Domain\ValueObject\Instant;

use function assert;

trait SerializationUtil
{
    protected function serializeInstant(Instant $value): string
    {
        return $value->format();
    }

    protected function deserializeInstant(string $value): Instant
    {
        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $value);
        assert($dateTime instanceof DateTimeImmutable);

        return Instant::fromDateTime($dateTime);
    }
}
