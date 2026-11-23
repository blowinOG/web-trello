<?php

declare(strict_types=1);

namespace App\Http\Responses;

abstract class AbstractResponse
{
    protected int $statusCode;

    /** @var array<string, string> */
    protected array $headers;

    /** @param array<string, string> $headers */
    public function __construct(int $statusCode = 200, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    abstract public function send(): void;

    protected function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }
}
