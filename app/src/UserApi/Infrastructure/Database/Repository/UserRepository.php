<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Database\Repository;

use Nette\Utils\Arrays;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository as UserRepositoryInterface;
use UserApi\Domain\ValueObject\PasswordHash;
use UserApi\Domain\ValueObject\User\UserId;
use UserApi\Infrastructure\Database\Repository\Trait\MutationUtil;
use UserApi\Infrastructure\Database\Repository\Trait\SerializationUtil;
use UserApi\Infrastructure\Database\SqlHandler\SqlHandler;

use function array_map;
use function assert;
use function is_string;

class UserRepository implements UserRepositoryInterface
{
    use SerializationUtil;
    use MutationUtil;

    public function __construct(
        private SqlHandler $sqlHandler,
    ) {
    }

    protected function getSqlHandler(): SqlHandler
    {
        return $this->sqlHandler;
    }

    protected function getTableName(): string
    {
        return 'users';
    }

    /** @return array<string, bool|float|int|string|null> */
    private function serializeEntity(User $user): array
    {
        return [
            'id' => $user->id->toString(),
            'name' => $user->name,
            'password_hash' => $user->passwordHash->toString(),
            'date_created' => $this->serializeInstant($user->dateCreated),
            'date_updated' => $this->serializeInstant($user->dateUpdated),
        ];
    }

    /** @param array<string, bool|float|int|string|null> $row */
    private function deserializeEntity(array $row): User
    {
        assert(is_string($row['id']));
        assert(is_string($row['name']));
        assert(is_string($row['password_hash']));
        assert(is_string($row['date_created']));
        assert(is_string($row['date_updated']));

        return new User(
            UserId::fromString($row['id']),
            $row['name'],
            PasswordHash::fromHash($row['password_hash']),
            $this->deserializeInstant($row['date_created']),
            $this->deserializeInstant($row['date_updated']),
        );
    }

    public function getById(UserId $id): User|null
    {
        $parameters = $this->sqlHandler->createParameterBag();
        $sql        = '
            SELECT u.*
            FROM users u
            WHERE u.id = ' . $parameters->add($id->toString()) . '
        ';

        $rows = $this->sqlHandler->query($sql, $parameters);

        $firstRow = Arrays::first($rows);
        if ($firstRow === null) {
            return null;
        }

        return $this->deserializeEntity($firstRow);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        $sql = 'SELECT u.* FROM users u';

        $rows = $this->sqlHandler->query($sql);

        return array_map(
            fn (array $row) => $this->deserializeEntity($row),
            $rows,
        );
    }

    public function generateId(): UserId
    {
        return UserId::generate();
    }

    public function create(User $user): void
    {
        $this->createRaw($this->serializeEntity($user));
    }

    public function update(User $user): void
    {
        $this->updateRaw($this->serializeEntity($user));
    }

    public function delete(User $user): void
    {
        $this->deleteRaw($user->id->toString());
    }
}
