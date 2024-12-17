<?php

declare(strict_types=1);

namespace UserApi\Application\Handler\User;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;

use function assert;

class UserDeleteHandler implements RequestHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute(User::class);
        assert($user instanceof User);

        $this->userRepository->delete($user);

        return new EmptyResponse();
    }
}
