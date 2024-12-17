<?php

declare(strict_types=1);

namespace UserApi\Application\Payload\User;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUser
{
    public function __construct(
        #[Assert\Length(min: 5)]
        public string $name,
        #[Assert\Length(min: 8)]
        public string $password,
    ) {
    }
}
