<?php

declare(strict_types=1);

namespace UserApi\Application\Model\User;

use DateTimeImmutable;
use UserApi\Domain\Entity\User;
use UserApi\Domain\ValueObject\User\UserId;

final readonly class PublicUser
{
    public function __construct(
        public UserId $id,
        public string $name,
        public DateTimeImmutable $dateCreated,
        public DateTimeImmutable $dateUpdated,
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->id,
            $user->name,
            $user->dateCreated->toDateTime(),
            $user->dateUpdated->toDateTime(),
        );
    }
}
