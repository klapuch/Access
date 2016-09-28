<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\{
    Storage, Encryption
};

/**
 * Password which belongs to particular user
 */
final class UserPassword implements Password {
    private $user;
    private $database;
    private $cipher;

    public function __construct(
    	User $user,
        Storage\Database $database,
        Encryption\Cipher $cipher
    ) {
        $this->user = $user;
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function change(string $password) {
        $this->database->query(
            'UPDATE users
            SET password = ?
            WHERE id = ?',
            [$this->cipher->encrypt($password), $this->user->id()]
        );
    }
}
