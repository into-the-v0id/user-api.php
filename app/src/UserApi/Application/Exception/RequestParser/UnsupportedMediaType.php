<?php

declare(strict_types=1);

namespace UserApi\Application\Exception\RequestParser;

use RuntimeException;
use Throwable;

class UnsupportedMediaType extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        Throwable|null $previous = null,
        private string|null $mediaType = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getMediaType(): string|null
    {
        return $this->mediaType;
    }
}
