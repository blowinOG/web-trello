<?php

declare(strict_types=1);

namespace App\Http\Responses;

final class JsonResponse extends AbstractResponse
{
    /** @var array<string, mixed> */
    private array $payload;

    /** @param array<string, mixed> $payload */
    public function __construct(array $payload, int $statusCode = 200)
    {
        parent::__construct($statusCode, ['Content-Type' => 'application/json; charset=utf-8']);
        $this->payload = $payload;
    }

    public function send(): void
    {
        $this->sendHeaders();
        echo json_encode($this->payload, JSON_UNESCAPED_UNICODE);
    }
}
