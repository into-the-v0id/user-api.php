<?php

declare(strict_types=1);

namespace Framework\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyHandler implements RequestHandlerInterface
{
    /** @var callable(): RequestHandlerInterface */
    private $handlerFactory;

    private RequestHandlerInterface|null $handler = null;

    /** @param callable(): RequestHandlerInterface $handlerFactory */
    public function __construct(callable $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    protected function getHandler(): RequestHandlerInterface
    {
        if ($this->handler === null) {
            $this->handler = ($this->handlerFactory)();
        }

        return $this->handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getHandler()->handle($request);
    }
}
