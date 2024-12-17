<?php

declare(strict_types=1);

namespace UserApi\Application\Exception\RequestParser;

use RuntimeException;

class SchemaViolation extends RuntimeException implements ParserException
{
}
