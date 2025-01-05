<?php

declare(strict_types=1);

namespace Framework\ServiceContainer\Exception;

use LogicException;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ServiceNotFound extends LogicException implements NotFoundExceptionInterface
{
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        protected string|null $serviceId = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getServiceId(): string|null
    {
        return $this->serviceId;
    }
}
