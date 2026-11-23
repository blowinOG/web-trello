<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\BoardController;
use App\Controllers\FrontendController;
use App\Database;
use App\Log;
use App\Routing\Router;
use App\Session\DatabaseSessionHandler;

ob_start();
$rawRequestBody = file_get_contents('php://input') ?: '';

register_shutdown_function(function () use ($rawRequestBody): void {
    $responseBody = '';
    if (ob_get_level() > 0) {
        $responseBody = (string) ob_get_contents();
        ob_end_flush();
    }

    Log::info('HTTP request handled', [
        'method'        => $_SERVER['REQUEST_METHOD'] ?? 'GET',
        'uri'           => $_SERVER['REQUEST_URI'] ?? '/',
        'query'         => $_GET,
        'post'          => $_POST,
        'request_body'  => $rawRequestBody,
        'status_code'   => http_response_code(),
        'response_body' => $responseBody,
    ]);
});

Database::boot();
session_set_save_handler(new DatabaseSessionHandler(3600), true);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();
$router
    ->get('/', [BoardController::class, 'index'])
    ->post('/cards/create', [BoardController::class, 'createCard'])
    ->post('/cards/move', [BoardController::class, 'moveCard'])
    ->get('/login', [FrontendController::class, 'showLogin'])
    ->post('/login', [FrontendController::class, 'login'])
    ->get('/register', [FrontendController::class, 'showRegister'])
    ->post('/register', [FrontendController::class, 'register'])
    ->post('/logout', [FrontendController::class, 'logout'])
    ->get('/logout', [FrontendController::class, 'logout']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/'
);
