<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Responses\JsonResponse;
use App\Http\Responses\RedirectResponse;
use App\Http\Responses\ViewResponse;

abstract class AbstractController
{
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    protected function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /** @param array<string, mixed> $data */
    protected function view(string $viewName, array $data = [], int $statusCode = 200): ViewResponse
    {
        return new ViewResponse($viewName, $data, $statusCode);
    }

    /** @param array<string, mixed> $data */
    protected function render(string $viewName, array $data = [], int $statusCode = 200): void
    {
        $this->view($viewName, $data, $statusCode)->send();
    }

    /** @param array<string, mixed> $payload */
    protected function json(array $payload, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse($payload, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): RedirectResponse
    {
        return new RedirectResponse($url, $statusCode);
    }
}
