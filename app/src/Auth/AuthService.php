<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\User;
use RuntimeException;

final class AuthService
{
    private const SESSION_USER_KEY = 'auth_user_id';

    private static ?User $cachedUser = null;

    public static function register(string $name, string $email, string $password): User
    {
        if (User::where('email', $email)->exists()) {
            throw new RuntimeException('Пользователь с таким email уже зарегистрирован.');
        }

        $user = User::create([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        self::loginUser($user);

        return $user;
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();
        if ($user === null || !password_verify($password, (string) $user->password_hash)) {
            return false;
        }

        self::loginUser($user);

        return true;
    }

    public static function loginUser(User $user): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION[self::SESSION_USER_KEY] = (int) $user->id;
        }
        self::$cachedUser = $user;
    }

    public static function user(): ?User
    {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $userId = $_SESSION[self::SESSION_USER_KEY] ?? null;
        if ($userId === null) {
            return null;
        }

        self::$cachedUser = User::find((int) $userId);
        if (self::$cachedUser === null) {
            unset($_SESSION[self::SESSION_USER_KEY]);
        }

        return self::$cachedUser;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        self::$cachedUser = null;
        unset($_SESSION[self::SESSION_USER_KEY]);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
