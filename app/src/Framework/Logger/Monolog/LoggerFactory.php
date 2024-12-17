<?php

declare(strict_types=1);

namespace Framework\Logger\Monolog;

use Framework\Logger\Config\LoggerConfig;
use InvalidArgumentException;
use Monolog\Handler\FilterHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use function assert;
use function sprintf;
use function strtolower;

class LoggerFactory
{
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        $loggerConfig = $container->get(LoggerConfig::class);
        assert($loggerConfig instanceof LoggerConfig);

        $logger = new Logger($loggerConfig->name);

        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new ProcessIdProcessor());

        $level = match (strtolower($loggerConfig->level)) {
            LogLevel::DEBUG => Level::Debug,
            LogLevel::INFO => Level::Info,
            LogLevel::NOTICE => Level::Notice,
            LogLevel::WARNING => Level::Warning,
            LogLevel::ERROR => Level::Error,
            LogLevel::CRITICAL => Level::Critical,
            LogLevel::ALERT => Level::Alert,
            LogLevel::EMERGENCY => Level::Emergency,
            default => throw new InvalidArgumentException(sprintf(
                'Unknown log level "%s"',
                $loggerConfig->level,
            ))
        };

        if ($loggerConfig->infoConsole) {
            $logger->pushHandler(new FilterHandler(
                new StreamHandler('php://stdout', $level),
                [
                    Level::Debug,
                    Level::Info,
                    Level::Notice,
                    Level::Warning,
                ],
            ));
        }

        if ($loggerConfig->errorConsole) {
            $logger->pushHandler(new FilterHandler(
                new StreamHandler('php://stderr', $level),
                [
                    Level::Error,
                    Level::Critical,
                    Level::Alert,
                    Level::Emergency,
                ],
            ));
        }

        if ($loggerConfig->infoFile !== null) {
            $logger->pushHandler(new FilterHandler(
                new StreamHandler($loggerConfig->infoFile, $level),
                [
                    Level::Debug,
                    Level::Info,
                    Level::Notice,
                    Level::Warning,
                ],
            ));
        }

        if ($loggerConfig->errorFile !== null) {
            $logger->pushHandler(new FilterHandler(
                new StreamHandler($loggerConfig->errorFile, $level),
                [
                    Level::Error,
                    Level::Critical,
                    Level::Alert,
                    Level::Emergency,
                ],
            ));
        }

        return $logger;
    }
}
