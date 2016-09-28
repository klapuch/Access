<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\{
    Storage, Encryption
};

/**
 * Secure entrance for logging users to the system
 */
final class SecureEntrance implements Entrance {
    private $database;
    private $cipher;

    public function __construct(
        Storage\Database $database,
        Encryption\Cipher $cipher
    ) {
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function enter(array $credentials): User {
        list($plainEmail, $plainPassword) = $credentials;
        $row = $this->database->fetch(
            'SELECT id, password
            FROM users  
            WHERE email IS NOT DISTINCT FROM ?',
            [$plainEmail]
        );
        if(!$this->exists($row)) {
            throw new \Exception(
                sprintf('Email "%s" does not exist', $plainEmail)
            );
        } elseif(!$this->cipher->decrypt($plainPassword, $row['password'])) {
            throw new \Exception('Wrong password');
        }
        if($this->cipher->deprecated($row['password']))
            $this->rehash($plainPassword, $row['id']);
        return new ConstantUser($row['id']);
    }

    /**
     * Does the record exist?
     * @param array $row
     * @return bool
     */
    private function exists(array $row): bool {
        return (bool)$row;
    }

    /**
     * Rehash the password with the newest one
     * @param string $password
     * @param int $id
     */
    private function rehash(string $password, int $id) {
        $this->database->query(
            'UPDATE users
            SET password = ?
            WHERE id IS NOT DISTINCT FROM ?',
            [$this->cipher->encrypt($password), $id]
        );
    }
}