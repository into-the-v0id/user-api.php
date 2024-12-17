<?php

declare(strict_types=1);

use Framework\Console\ConsoleModule;
use Framework\Database\DatabaseModule;
use Framework\Logger\LoggerModule;
use Framework\Server\ServerModule;
use UserApi\UserApiModule;

return [
    // Framework
    new ServerModule(),
    new ConsoleModule(),
    new LoggerModule(),
    new DatabaseModule(),

    // User API
    new UserApiModule(),
];
