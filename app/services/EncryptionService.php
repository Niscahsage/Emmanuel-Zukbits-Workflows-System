<?php
// app/services/EncryptionService.php
// EncryptionService provides methods to encrypt and decrypt sensitive data fields.
namespace App\services;

use RuntimeException;

class EncryptionService
{
    // Encrypt plaintext with AES-256-CBC using ENCRYPTION_KEY
    public function encrypt(string $plaintext): string
    {
        if ($plaintext === '') {
            throw new RuntimeException('Cannot encrypt empty value.');
        }

        $cfg = security_config();
        $key = $cfg['encryption_key'] ?? null;

        if (!$key) {
            throw new RuntimeException('ENCRYPTION_KEY is not configured.');
        }

        $keyBin = hash('sha256', $key, true);
        $iv     = random_bytes(16);

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-cbc',
            $keyBin,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($ciphertext === false) {
            throw new RuntimeException('Encryption failed.');
        }

        $payload = base64_encode($iv . $ciphertext);
        return $payload;
    }

    // Decrypt payload created by encrypt()
    public function decrypt(string $payload): string
    {
        $cfg = security_config();
        $key = $cfg['encryption_key'] ?? null;

        if (!$key) {
            throw new RuntimeException('ENCRYPTION_KEY is not configured.');
        }

        $keyBin = hash('sha256', $key, true);

        $data = base64_decode($payload, true);
        if ($data === false || strlen($data) < 17) {
            throw new RuntimeException('Invalid encrypted payload.');
        }

        $iv         = substr($data, 0, 16);
        $ciphertext = substr($data, 16);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-cbc',
            $keyBin,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plaintext === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $plaintext;
    }
}
