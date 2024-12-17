<?php

// phpcs:disable SlevomatCodingStandard.Commenting.ForbiddenAnnotations.AnnotationForbidden

/**
 * @copyright Oliver Amann
 * @license AGPL-3.0-only
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

use Dotenv\Dotenv;
use Framework\Module\Module;
use Framework\ServiceContainer\InvokerContainer;
use Framework\ServiceContainer\ServiceContainer;
use Invoker\Invoker;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;

chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

Dotenv::createUnsafeMutable(__DIR__)
    ->safeLoad();

$container = new ServiceContainer();

$container->factory(
    ContainerInterface::class,
    static fn (ContainerInterface $container) => $container,
);

/** @var Module[] $modules */
$modules = require __DIR__ . '/modules.php';

foreach ($modules as $module) {
    $module->registerServices($container);
}

$invokerContainer = new InvokerContainer(
    new Invoker(new ResolverChain([
        new TypeHintContainerResolver($container),
        new DefaultValueResolver(),
    ])),
);
$container->setFallbackContainer($invokerContainer);

foreach ($modules as $module) {
    $module->bootstrap($container);
}

return $container;
