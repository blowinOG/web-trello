<?php

declare(strict_types=1);

namespace App\Routing;

use App\Http\Responses\AbstractResponse;
use App\Http\Responses\JsonResponse;

final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [];

    public function get(string $path, array $handler): self
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
        return $this;
    }

    public function post(string $path, array $handler): self
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $path = $this->normalize((string) parse_url($uri, PHP_URL_PATH));

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            (new JsonResponse([
                'status'  => 'error',
                'message' => "Маршрут [{$method} {$path}] не найден",
            ], 404))->send();
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        $response = $controller->$action();

        if ($response instanceof AbstractResponse) {
            $response->send();
        }
    }

    private function normalize(string $path): string
    {
        $trimmed = '/' . trim($path, '/');
        return $trimmed === '//' ? '/' : $trimmed;
    }
}
