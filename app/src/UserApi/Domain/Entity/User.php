<?php

declare(strict_types=1);

namespace UserApi\Domain\Entity;

use UserApi\Domain\ValueObject\Instant;
use UserApi\Domain\ValueObject\PasswordHash;
use UserApi\Domain\ValueObject\User\UserId;

final class User
{
    public function __construct(
        public UserId $id,
        public string $name,
        public PasswordHash $passwordHash,
        public Instant $dateCreated,
        public Instant $dateUpdated,
    ) {
    }
}
