<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Forgetful user is the one who forget password
 */
final class ForgetfulUser implements User {
    private $email;
    private $database;

    public function __construct(string $email, Storage\Database $database) {
        $this->email = $email;
        $this->database = $database;
    }

    public function id(): int {
        return (int)$this->database->fetchColumn(
            'SELECT id
            FROM users
            WHERE email IS NOT DISTINCT FROM ?',
            [$this->email]
        );
    }
}