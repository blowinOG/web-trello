<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Crypto;

$plain = $argv[1] ?? '';
if ($plain === '') {
    echo "Использование: php encrypt.php \"значение_для_шифрования\"" . PHP_EOL;
    exit(1);
}

$crypto = new Crypto();
echo $crypto->encrypt($plain) . PHP_EOL;
