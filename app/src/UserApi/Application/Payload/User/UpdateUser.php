<?php

declare(strict_types=1);

namespace UserApi\Application\Payload\User;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use UserApi\Domain\ValueObject\User\UserId;

final readonly class UpdateUser
{
    public function __construct(
        public UserId $id,
        #[Assert\Length(min: 5)]
        public string $name,
        public DateTimeImmutable $dateCreated,
        public DateTimeImmutable $dateUpdated,
    ) {
    }
}
