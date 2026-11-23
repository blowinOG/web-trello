<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Crypto;

$encrypted = $argv[1] ?? '';
if ($encrypted === '') {
    echo "Использование: php decrypt.php \"enc:...\"" . PHP_EOL;
    exit(1);
}

$crypto = new Crypto();
echo $crypto->decrypt($encrypted) . PHP_EOL;
