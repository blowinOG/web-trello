<?php
require __DIR__ . '/../vendor/autoload.php';

// загрузка .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// инициализация БД (Capsule)
require __DIR__ . '/../src/Database.php';

// логгер
$log = require __DIR__ . '/../src/Logger.php';

// логирование входящего запроса
$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];
$body   = file_get_contents('php://input');
$log->info('Incoming request', ['method' => $method, 'uri' => $uri, 'body' => $body]);

// пример работы с Eloquent
use App\Models\User;
$users = User::all();

// формируем ответ
$response = json_encode(['users' => $users]);

// логируем тело ответа
$log->info('Response body', ['body' => $response]);

header('Content-Type: application/json');
echo $response;