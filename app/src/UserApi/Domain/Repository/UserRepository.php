<?php

declare(strict_types=1);

namespace UserApi\Domain\Repository;

use UserApi\Domain\Entity\User;
use UserApi\Domain\ValueObject\User\UserId;

interface UserRepository
{
    public function getById(UserId $id): User|null;

    /** @return User[] */
    public function getAll(): array;

    public function generateId(): UserId;

    public function create(User $user): void;

    public function update(User $user): void;

    public function delete(User $user): void;
}
