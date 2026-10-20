<?php
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => 'pgsql',
    'host'     => 'postgres',  // имя сервиса из docker-compose.yml
    'port'     => '5432',
    'database' => 'trello',
    'username' => 'postgres',
    'password' => 'postgres',
    'charset'  => 'utf8',
    'prefix'   => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();