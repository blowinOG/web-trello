<?php

declare(strict_types=1);

namespace App\Http\Responses;

final class RedirectResponse extends AbstractResponse
{
    public function __construct(string $url, int $statusCode = 302)
    {
        parent::__construct($statusCode, ['Location' => $url]);
    }

    public function send(): void
    {
        $this->sendHeaders();
    }
}
