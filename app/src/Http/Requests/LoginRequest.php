<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Auth\AuthService;
use App\Http\Responses\AbstractResponse;
use App\Http\Responses\RedirectResponse;
use App\Http\Validation\ValidationException;
use App\Models\User;

final class LoginRequest extends AbstractRequest
{
    private ?User $authenticatedUser = null;

    protected function rules(): array
    {
        return [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    protected function validateLogic(): void
    {
        $email = (string) $this->get('email');
        $password = (string) $this->get('password');

        $user = User::where('email', $email)->first();
        if ($user === null || !password_verify($password, (string) $user->password_hash)) {
            throw new ValidationException([
                'email' => 'Неверный email или пароль.',
            ]);
        }

        $this->authenticatedUser = $user;
    }

    protected function handle(): AbstractResponse
    {
        if ($this->authenticatedUser !== null) {
            AuthService::loginUser($this->authenticatedUser);
        }

        $_SESSION['flash'] = 'Вы успешно вошли в систему.';

        return new RedirectResponse('/');
    }
}
