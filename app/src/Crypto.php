<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class Crypto
{
    private const CIPHER = 'aes-256-cbc';
    public const PREFIX = 'enc:';

    private string $binaryKey;

    public function __construct(?string $secretKey = null)
    {
        $rawKey = $secretKey ?? (string) getenv('APP_KEY');
        if ($rawKey === '') {
            throw new RuntimeException('Переменная окружения APP_KEY не задана.');
        }

        $this->binaryKey = hash('sha256', $rawKey, true);
    }

    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = random_bytes($ivLength);

        $cipherText = openssl_encrypt($plainText, self::CIPHER, $this->binaryKey, OPENSSL_RAW_DATA, $iv);
        if ($cipherText === false) {
            throw new RuntimeException('Ошибка при шифровании данных через OpenSSL.');
        }

        return self::PREFIX . base64_encode($iv . $cipherText);
    }

    public function decrypt(string $payload): string
    {
        if (!self::isEncrypted($payload)) {
            return $payload;
        }

        $decoded = base64_decode(substr($payload, strlen(self::PREFIX)), true);
        if ($decoded === false) {
            throw new RuntimeException('Некорректный формат зашифрованной строки (base64).');
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        if (strlen($decoded) <= $ivLength) {
            throw new RuntimeException('Слишком короткие зашифрованные данные.');
        }

        $iv = substr($decoded, 0, $ivLength);
        $cipherText = substr($decoded, $ivLength);

        $plainText = openssl_decrypt($cipherText, self::CIPHER, $this->binaryKey, OPENSSL_RAW_DATA, $iv);
        if ($plainText === false) {
            throw new RuntimeException('Ошибка дешифрования: неверный APP_KEY или поврежденные данные.');
        }

        return $plainText;
    }

    public static function isEncrypted(mixed $value): bool
    {
        return is_string($value) && str_starts_with($value, self::PREFIX);
    }
}
