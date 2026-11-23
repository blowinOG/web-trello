<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Auth\AuthService;
use App\Http\Responses\AbstractResponse;
use App\Http\Responses\RedirectResponse;
use App\Http\Validation\ValidationException;
use App\Models\User;

final class RegisterRequest extends AbstractRequest
{
    protected function rules(): array
    {
        return [
            'name'     => 'required|min:2|max:100',
            'email'    => 'required|email|max:150',
            'password' => 'required|min:6|max:100|confirmed',
        ];
    }

    protected function validateLogic(): void
    {
        $email = (string) $this->get('email');
        if (User::where('email', $email)->exists()) {
            throw new ValidationException([
                'email' => 'Этот email уже занят другим пользователем.',
            ]);
        }
    }

    protected function handle(): AbstractResponse
    {
        AuthService::register(
            (string) $this->get('name'),
            (string) $this->get('email'),
            (string) $this->get('password')
        );

        $_SESSION['flash'] = 'Регистрация прошла успешно! Добро пожаловать в Trello.';

        return new RedirectResponse('/');
    }
}
