<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Trello Clone App',
    ],
    'db' => [
        'host'     => getenv('DB_HOST') ?: 'postgres',
        'port'     => getenv('DB_PORT') ?: '5432',
        'database' => getenv('DB_NAME') ?: 'app',
        'username' => getenv('DB_USER') ?: 'app',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],
];
