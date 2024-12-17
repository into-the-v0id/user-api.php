<?php

declare(strict_types=1);

use Framework\Console\ConsoleModule;
use Framework\Database\DatabaseModule;
use Framework\Logger\LoggerModule;
use Framework\Server\ServerModule;
use UserApi\UserApiModule;

return [
    // Framework
    new LoggerModule(),
    new ConsoleModule(),
    new DatabaseModule(),
    new ServerModule(),

    // User API
    new UserApiModule(),
];
