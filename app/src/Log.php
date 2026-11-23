<?php

declare(strict_types=1);

namespace App;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

final class Log
{
    private static ?Logger $logger = null;

    public static function requestId(): string
    {
        return (string) ($_SERVER['HTTP_X_REQUEST_ID'] ?? '');
    }

    public static function getLogger(): Logger
    {
        if (self::$logger !== null) {
            return self::$logger;
        }

        $logger = new Logger('app');
        $formatter = new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, true);

        // 1. Запись в файл app/logs/app.log (всегда доступна на хосте и в контейнере)
        $logDir = dirname(__DIR__) . '/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $fileHandler = new StreamHandler($logDir . '/app.log', Level::Debug);
        $fileHandler->setFormatter($formatter);
        $logger->pushHandler($fileHandler);

        // 2. Отправка в stderr через системный механизм PHP-FPM для docker compose logs
        $dockerHandler = new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, Level::Debug);
        $dockerHandler->setFormatter($formatter);
        $logger->pushHandler($dockerHandler);

        $logger->pushProcessor(function (LogRecord $record): LogRecord {
            $extra = $record->extra;
            $extra['log_level'] = strtolower($record->level->name);
            $extra['request_id'] = self::requestId();
            $extra['route'] = (string) ($_SERVER['SCRIPT_NAME'] ?? '');

            return $record->with(extra: $extra);
        });

        self::$logger = $logger;

        return $logger;
    }

    public static function info(string $message, array $context = []): void
    {
        self::getLogger()->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::getLogger()->error($message, $context);
    }
}
