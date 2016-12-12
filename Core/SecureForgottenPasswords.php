<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Works just with secure forgotten passwords
 */
final class SecureForgottenPasswords implements ForgottenPasswords {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function remind(string $email): void {
        $reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
        $this->database->query(
            'INSERT INTO forgotten_passwords (user_id, reminder, reminded_at) VALUES
            ((SELECT id FROM users WHERE email IS NOT DISTINCT FROM ?), ?, NOW())',
            [$email, $reminder]
        );
    }
}