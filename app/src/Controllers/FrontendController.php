<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\AbstractResponse;
use App\Http\Validation\ValidationException;

final class FrontendController extends AbstractController
{
    public function showLogin(): AbstractResponse
    {
        if (AuthService::check()) {
            return $this->redirect('/');
        }

        return $this->view('auth/login', [
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function login(): AbstractResponse
    {
        try {
            $request = new LoginRequest();
            return $request->execute();
        } catch (ValidationException $e) {
            return $this->view('auth/login', [
                'errors' => $e->errors(),
                'old'    => ['email' => (string) $this->input('email', '')],
            ], 422);
        }
    }

    public function showRegister(): AbstractResponse
    {
        if (AuthService::check()) {
            return $this->redirect('/');
        }

        return $this->view('auth/register', [
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function register(): AbstractResponse
    {
        try {
            $request = new RegisterRequest();
            return $request->execute();
        } catch (ValidationException $e) {
            return $this->view('auth/register', [
                'errors' => $e->errors(),
                'old'    => [
                    'name'  => (string) $this->input('name', ''),
                    'email' => (string) $this->input('email', ''),
                ],
            ], 422);
        }
    }

    public function logout(): AbstractResponse
    {
        AuthService::logout();
        return $this->redirect('/login');
    }
}
