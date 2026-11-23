<?php

declare(strict_types=1);

namespace App\Http\Responses;

use RuntimeException;

final class ViewResponse extends AbstractResponse
{
    private string $viewName;

    /** @var array<string, mixed> */
    private array $data;

    /** @param array<string, mixed> $data */
    public function __construct(string $viewName, array $data = [], int $statusCode = 200)
    {
        parent::__construct($statusCode, ['Content-Type' => 'text/html; charset=utf-8']);
        $this->viewName = $viewName;
        $this->data = $data;
    }

    public function send(): void
    {
        $viewPath = dirname(__DIR__, 3) . '/views/' . ltrim($this->viewName, '/') . '.php';
        if (!is_file($viewPath)) {
            throw new RuntimeException("Шаблон представления не найден: {$viewPath}");
        }

        $this->sendHeaders();
        extract($this->data, EXTR_SKIP);
        require $viewPath;
    }
}
