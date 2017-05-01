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
		if (!$this->exists($email))
			throw new \UnexpectedValueException('The email does not exist');
		$reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
		(new Storage\ParameterizedQuery(
			$this->database,
			'INSERT INTO forgotten_passwords (user_id, reminder, reminded_at, used) VALUES
			(?, ?, NOW(), FALSE)',
			[$this->id($email), $reminder]
		))->execute();
	}

	/**
	 * Does the email exist?
	 * @param string $email
	 * @return bool
	 */
	private function exists(string $email): bool {
		return (bool)$this->id($email);
	}

	/**
	 * ID matching the email, if any
	 * @param string $email
	 * @return int
	 */
	private function id(string $email): int {
		return (int)(new Storage\ParameterizedQuery(
			$this->database,
			'SELECT ID
			FROM users
			WHERE email IS NOT DISTINCT FROM ?',
			[$email]
		))->field();
	}
}