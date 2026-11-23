<?php

declare(strict_types=1);

namespace App\Http\Validation;

use RuntimeException;

final class ValidationException extends RuntimeException
{
    /** @var array<string, string> */
    private array $errors;

    /** @param array<string, string> $errors */
    public function __construct(array $errors, string $message = 'Ошибка валидации данных')
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
