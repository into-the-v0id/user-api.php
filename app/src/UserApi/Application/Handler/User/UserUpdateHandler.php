<?php

declare(strict_types=1);

namespace UserApi\Application\Handler\User;

use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserApi\Application\Model\User\PublicUser;
use UserApi\Application\Payload\User\UpdateUser;
use UserApi\Application\Service\DataResponseGenerator;
use UserApi\Application\Service\ErrorResponseGenerator;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;
use UserApi\Domain\ValueObject\Instant;

use function assert;

class UserUpdateHandler implements RequestHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private DataResponseGenerator $dataResponseGenerator,
        private ErrorResponseGenerator $errorResponseGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute(User::class);
        assert($user instanceof User);

        $payload = $request->getParsedBody();
        assert($payload instanceof UpdateUser);

        if (! $payload->id->equals($user->id)) {
            return $this->errorResponseGenerator->generate(400, $request, description: 'Mismatching ID');
        }

        if (
            $payload->dateCreated->format(DateTimeInterface::ATOM)
            !== $user->dateCreated->format(DateTimeInterface::ATOM)
        ) {
            return $this->errorResponseGenerator->generate(409, $request, description: 'Conflicting dateCreated');
        }

        if (
            $payload->dateUpdated->format(DateTimeInterface::ATOM)
            !== $user->dateUpdated->format(DateTimeInterface::ATOM)
        ) {
            return $this->errorResponseGenerator->generate(409, $request, description: 'Conflicting dateUpdated');
        }

        if ($payload->name !== $user->name) {
            $user->name        = $payload->name;
            $user->dateUpdated = Instant::now();
        }

        $this->userRepository->update($user);

        $publicUser = PublicUser::fromEntity($user);

        return $this->dataResponseGenerator->generate($publicUser, $request);
    }
}
