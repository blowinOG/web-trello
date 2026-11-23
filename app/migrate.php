<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Database;

$executed = Database::runMigrations(__DIR__ . '/migrations');

if ($executed === []) {
    echo "Новых миграций для выполнения нет." . PHP_EOL;
} else {
    foreach ($executed as $migration) {
        echo "Выполнена миграция: {$migration}" . PHP_EOL;
    }
}
