<?php
namespace App\App;

abstract class Crypt
{
    public static function encrypt(string $data): string
    {
        if (!openssl_public_encrypt($data, $encrypted, self::getKey('public')))
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');

        return base64_encode($encrypted);
    }

    public static function decrypt(string $data): string
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, self::getKey('private')))
            return $decrypted;
            
        return '';
    }
    private static function getKey(string $type): string
    {
        $keyPath = H . '/sys/key/' . $type . '.pem';
        if (!file_exists($keyPath)) {
            throw new \Exception('File key not exists!');
        }
        return file_get_contents($keyPath);
    }
}
