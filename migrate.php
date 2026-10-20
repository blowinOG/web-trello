<?php
require __DIR__ . '/vendor/autoload.php';

// .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Capsule (Eloquent)
require __DIR__ . '/src/Database.php';

$log = require __DIR__ . '/src/Logger.php';

$files = glob(__DIR__ . '/database/migrations/*.php');
sort($files);

foreach ($files as $file) {
    $log->info('Running migration', ['file' => basename($file)]);
    echo "Migrating: " . basename($file) . PHP_EOL;
    $migration = require $file;
    $migration->up();
}

echo "All migrations applied." . PHP_EOL;