<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Works just with secure forgotten passwords
 */
final class SecureForgottenPasswords implements ForgottenPasswords {
    private $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function remind(string $email): void {
		$reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
		(new Storage\ParameterizedQuery(
			$this->database,
            'INSERT INTO forgotten_passwords (user_id, reminder, reminded_at) VALUES
            ((SELECT id FROM users WHERE email IS NOT DISTINCT FROM ?), ?, NOW())',
            [$email, $reminder]
		))->execute();
    }
}