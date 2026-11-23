<?php

declare(strict_types=1);

namespace App;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use PDO;

final class Database
{
    private static ?Capsule $capsule = null;

    public static function boot(): Capsule
    {
        if (self::$capsule !== null) {
            return self::$capsule;
        }

        $host = getenv('DB_HOST') ?: 'postgres';
        $port = getenv('DB_PORT') ?: '5432';
        $name = getenv('DB_NAME') ?: 'app';
        $user = getenv('DB_USER') ?: 'app';
        $password = getenv('DB_PASSWORD') ?: '';

        $config = [
            'driver'   => 'pgsql',
            'host'     => $host,
            'port'     => $port,
            'database' => $name,
            'username' => $user,
            'password' => $password,
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'options'  => [
                PDO::ATTR_TIMEOUT => 3,
            ],
        ];

        $requestId = Log::requestId();
        if ($requestId !== '' && preg_match('/^[A-Za-z0-9-]{1,63}$/', $requestId) === 1) {
            $config['application_name'] = $requestId;
        }

        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setEventDispatcher(new Dispatcher(new Container()));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$capsule = $capsule;

        return $capsule;
    }

    public static function connect(): PDO
    {
        return self::boot()->getConnection()->getPdo();
    }

    public static function runMigrations(string $migrationsPath): array
    {
        $capsule = self::boot();
        $resolver = $capsule->getDatabaseManager();

        $repository = new DatabaseMigrationRepository($resolver, 'migrations');
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }

        $migrator = new Migrator($repository, $resolver, new Filesystem());

        return $migrator->run([$migrationsPath]);
    }
}
