<?php

declare(strict_types=1);

namespace UserApi\Application\Handler\User;

use Framework\Server\Router\UriBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserApi\Application\Model\User\PublicUser;
use UserApi\Application\Payload\User\CreateUser;
use UserApi\Application\Service\DataResponseGenerator;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;
use UserApi\Domain\ValueObject\Instant;
use UserApi\Domain\ValueObject\PasswordHash;

use function assert;

class UserCreateHandler implements RequestHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private DataResponseGenerator $dataResponseGenerator,
        private UriBuilder $uriBuilder,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $payload = $request->getParsedBody();
        assert($payload instanceof CreateUser);

        $now = Instant::now();

        $user = new User(
            $this->userRepository->generateId(),
            $payload->name,
            PasswordHash::fromPlainPassword($payload->password),
            $now,
            $now,
        );

        $this->userRepository->create($user);

        $publicUser = PublicUser::fromEntity($user);

        return $this->dataResponseGenerator->generate($publicUser, $request)
            ->withStatus(201)
            ->withHeader(
                'Location',
                $this->uriBuilder->buildUri('user.detail', ['userId' => $user->id]),
            );
    }
}
