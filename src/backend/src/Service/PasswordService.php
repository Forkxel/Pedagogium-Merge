<?php
namespace App\Service;

class PasswordService
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        if ($hash === false) {
            throw new \RuntimeException('Password hashing failed');
        }

        return $hash;
    }

    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT);
    }
}
