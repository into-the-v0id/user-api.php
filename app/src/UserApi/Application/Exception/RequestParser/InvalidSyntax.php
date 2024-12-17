<?php

declare(strict_types=1);

namespace UserApi\Application\Exception\RequestParser;

use UnexpectedValueException;

class InvalidSyntax extends UnexpectedValueException implements ParserException
{
}
