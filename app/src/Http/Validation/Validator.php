<?php

declare(strict_types=1);

namespace App\Http\Validation;

final class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $rules Пример: ['email' => 'required|email|max:150', 'password' => 'required|min:6']
     * @return array<string, mixed> Очищенные данные
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        $validated = [];

        foreach ($rules as $field => $ruleString) {
            $rawValue = $data[$field] ?? null;
            $value = is_string($rawValue) ? trim($rawValue) : $rawValue;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field] = "Поле «{$field}» обязательно для заполнения.";
                    break;
                }

                if ($value === null || $value === '') {
                    continue;
                }

                if ($rule === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $errors[$field] = "Поле «{$field}» должно быть корректным email.";
                    break;
                }

                if ($rule === 'int' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $errors[$field] = "Поле «{$field}» должно быть целым числом.";
                    break;
                }

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (mb_strlen((string) $value) < $min) {
                        $errors[$field] = "Поле «{$field}» должно содержать минимум {$min} символов.";
                        break;
                    }
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (mb_strlen((string) $value) > $max) {
                        $errors[$field] = "Поле «{$field}» должно содержать максимум {$max} символов.";
                        break;
                    }
                }

                if ($rule === 'confirmed') {
                    $confirmValue = trim((string) ($data[$field . '_confirmation'] ?? ''));
                    if ((string) $value !== $confirmValue) {
                        $errors[$field] = "Подтверждение поля «{$field}» не совпадает.";
                        break;
                    }
                }
            }

            if (!isset($errors[$field])) {
                $validated[$field] = $value;
            }
        }

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        return $validated;
    }
}
