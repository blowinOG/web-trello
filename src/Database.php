<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => $_ENV['DB_DRIVER'] ?? 'pgsql',
    'host'     => $_ENV['DB_HOST']   ?? '127.0.0.1',
    'port'     => $_ENV['DB_PORT']   ?? '5432',
    'database' => $_ENV['DB_NAME']   ?? 'trello',
    'username' => $_ENV['DB_USER']   ?? 'postgres',
    'password' => $_ENV['DB_PASS']   ?? 'postgres',
    'charset'  => 'utf8',
    'prefix'   => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();