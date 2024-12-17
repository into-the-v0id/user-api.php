<?php

declare(strict_types=1);

namespace Framework\Server;

use Laminas\Stratigility\Next;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class MiddlewareStack implements MiddlewareInterface
{
    /** @var SplQueue<MiddlewareInterface> */
    private SplQueue $middlewares;

    /** @param iterable<MiddlewareInterface> $middlewares */
    public function __construct(iterable $middlewares = [])
    {
        $this->middlewares = new SplQueue();

        foreach ($middlewares as $middleware) {
            $this->push($middleware);
        }
    }

    public function push(MiddlewareInterface $middleware): void
    {
        $this->middlewares->enqueue($middleware);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return (new Next($this->middlewares, $handler))->handle($request);
    }
}
