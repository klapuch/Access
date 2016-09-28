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

    public function remind(string $email) {
        $reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
        $id = $this->toId($email);
        $this->database->query(
            'INSERT INTO forgotten_passwords (user_id, reminder, reminded_at) VALUES
            (?, ?, NOW())',
            [$id, $reminder]
        );
    }

    /**
     * Email to ID
	 * @param string $email
     * @return int
     */
    private function toId(string $email): int {
        return (int)$this->database->fetchColumn(
            'SELECT id FROM users WHERE email IS NOT DISTINCT FROM ?',
            [$email]
        );
    }
}
