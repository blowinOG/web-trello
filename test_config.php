<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = new Src\Config(__DIR__ . '/config/app.php', $_ENV['APP_ENCRYPTION_KEY']);

$encrypted = $config->encrypt('super_secret');
echo "Encrypted: $encrypted\n";
echo "Decrypted: " . $config->decrypt(substr($encrypted, 4)) . "\n";