<?php
namespace Src;

class Config
{
    private array $data = [];
    private string $key;

    public function __construct(string $configPath, string $key)
    {
        $this->key  = $key;
        $this->data = require $configPath;
    }

    public function get(string $key, $default = null)
    {
        $value = $this->data[$key] ?? $default;
        if (is_string($value) && str_starts_with($value, 'enc:')) {
            $encoded = substr($value, 4);
            if ($encoded === '') {
                return $default;
            }
            return $this->decrypt($encoded);
        }
        return $value;
    }

    public function encrypt(string $plain): string
    {
        $iv     = random_bytes(16);
        $cipher = openssl_encrypt($plain, 'aes-256-cbc', $this->key, 0, $iv);
        return 'enc:' . base64_encode($iv . $cipher);
    }

    public function decrypt(string $encoded): string
    {
        $raw    = base64_decode($encoded);
        $iv     = substr($raw, 0, 16);
        $cipher = substr($raw, 16);
        return openssl_decrypt($cipher, 'aes-256-cbc', $this->key, 0, $iv) ?: '';
    }
}