<?php

declare(strict_types=1);

namespace UserApi\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

use function assert;
use function is_string;
use function password_get_info;
use function password_hash;
use function password_verify;
use function sprintf;

use const PASSWORD_ARGON2ID;

final readonly class PasswordHash implements Stringable, JsonSerializable
{
    private const ALGORITHM = PASSWORD_ARGON2ID;

    private function __construct(
        private string $hash,
    ) {
    }

    public static function fromPlainPassword(string $plainPassword): self
    {
        $hash = password_hash($plainPassword, self::ALGORITHM);

        return new self($hash);
    }

    public static function fromHash(string $hash): self
    {
        $hashInfo      = password_get_info($hash);
        $hashAlgorithm = $hashInfo['algo'];
        assert(is_string($hashAlgorithm));

        if ($hashAlgorithm !== self::ALGORITHM) {
            throw new InvalidArgumentException(sprintf(
                'Invalid password hash "%s". Expected %s hash, got %s',
                $hash,
                self::ALGORITHM,
                $hashAlgorithm,
            ));
        }

        return new self($hash);
    }

    public function toString(): string
    {
        return $this->hash;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    public function verify(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->hash);
    }
}
