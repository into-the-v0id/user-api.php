<?php

declare(strict_types=1);

namespace UserApi\Application\Middleware\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;
use UserApi\Application\Service\ErrorResponseGenerator;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;
use UserApi\Domain\ValueObject\User\UserId;

use function assert;
use function is_string;

class Load implements MiddlewareInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private ErrorResponseGenerator $errorResponseGenerator,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $rawUserId = $request->getAttribute('userId');
        if ($rawUserId === null) {
            throw new RuntimeException('Missing "userId" request attribute');
        }

        assert(is_string($rawUserId));

        try {
            $userId = UserId::fromString($rawUserId);
        } catch (Throwable) {
            return $this->errorResponseGenerator->generate(
                400,
                $request,
                description: 'Invalid Identifier',
            );
        }

        $user = $this->userRepository->getById($userId);
        if ($user === null) {
            return $this->errorResponseGenerator->generate(404, $request);
        }

        $request = $request->withAttribute(User::class, $user);

        return $handler->handle($request);
    }
}
