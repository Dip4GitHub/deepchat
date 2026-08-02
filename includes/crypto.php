<?php
if (!defined('CHAT_ENCRYPTION_KEY')) {
    define('CHAT_ENCRYPTION_KEY', 'deepchat-secret-key-2026');
}

function chat_get_key(): string
{
    return hash('sha256', CHAT_ENCRYPTION_KEY, true);
}

function chat_encrypt_message(string $plain): string
{
    $key = chat_get_key();
    $iv = random_bytes(16);
    $cipherText = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipherText);
}

function chat_decrypt_message(string $payload): string
{
    $data = base64_decode($payload, true);
    if ($data === false || strlen($data) < 17) {
        return '';
    }

    $iv = substr($data, 0, 16);
    $cipherText = substr($data, 16);

    $plain = openssl_decrypt($cipherText, 'AES-256-CBC', chat_get_key(), OPENSSL_RAW_DATA, $iv);
    return $plain === false ? '' : $plain;
}
