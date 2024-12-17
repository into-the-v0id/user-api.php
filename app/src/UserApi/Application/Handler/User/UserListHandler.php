<?php

declare(strict_types=1);

namespace UserApi\Application\Handler\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserApi\Application\Model\User\PublicUser;
use UserApi\Application\Service\DataResponseGenerator;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;

use function array_map;

class UserListHandler implements RequestHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private DataResponseGenerator $dataResponseGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $users = $this->userRepository->getAll();

        $publicUsers = array_map(
            static fn (User $user) => PublicUser::fromEntity($user),
            $users,
        );

        return $this->dataResponseGenerator->generate($publicUsers, $request);
    }
}
