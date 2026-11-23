<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class Config
{
    /** @var array<string, mixed> */
    private static array $items = [];

    private static bool $loaded = false;

    private static ?Crypto $crypto = null;

    public static function boot(?string $configDir = null, ?Crypto $crypto = null): void
    {
        $dir = $configDir ?? dirname(__DIR__) . '/config';
        $env = getenv('APP_ENV') ?: 'local';

        $items = [];

        $defaultFile = $dir . '/default.php';
        if (is_file($defaultFile)) {
            $items = require $defaultFile;
        }

        $envFile = $dir . '/' . basename($env) . '.php';
        if (!is_file($envFile)) {
            throw new RuntimeException("Файл конфигурации для окружения '{$env}' не найден: {$envFile}");
        }

        $envItems = require $envFile;
        self::$items = array_replace_recursive($items, $envItems);
        self::$crypto = $crypto;
        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!self::$loaded) {
            self::boot();
        }

        $segments = explode('.', $key);
        $value = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        if (Crypto::isEncrypted($value)) {
            return self::crypto()->decrypt($value);
        }

        return $value;
    }

    public static function set(string $key, mixed $value, bool $encrypt = false): void
    {
        if (!self::$loaded) {
            self::boot();
        }

        if ($encrypt && is_string($value)) {
            $value = self::crypto()->encrypt($value);
        }

        $segments = explode('.', $key);
        $target = &self::$items;

        foreach ($segments as $segment) {
            if (!isset($target[$segment]) || !is_array($target[$segment])) {
                $target[$segment] = [];
            }
            $target = &$target[$segment];
        }

        $target = $value;
    }

    private static function crypto(): Crypto
    {
        if (self::$crypto === null) {
            self::$crypto = new Crypto();
        }

        return self::$crypto;
    }
}
