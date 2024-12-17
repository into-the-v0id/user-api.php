<?php

declare(strict_types=1);

namespace UserApi\Application\Handler\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserApi\Application\Model\User\PublicUser;
use UserApi\Application\Service\DataResponseGenerator;
use UserApi\Domain\Entity\User;

use function assert;

class UserDetailHandler implements RequestHandlerInterface
{
    public function __construct(
        private DataResponseGenerator $dataResponseGenerator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute(User::class);
        assert($user instanceof User);

        $publicUser = PublicUser::fromEntity($user);

        return $this->dataResponseGenerator->generate($publicUser, $request);
    }
}
