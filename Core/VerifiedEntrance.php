<?php
declare(strict_types = 1);

namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Verified entrance
 */
final class VerifiedEntrance implements Entrance {
	private $database;
	private $origin;

	public function __construct(\PDO $database, Entrance $origin) {
		$this->database = $database;
		$this->origin = $origin;
	}

	public function enter(array $credentials): User {
		[$email] = $credentials;
		if (!$this->verified($email))
			throw new \Exception('Email has not been verified yet');
		return $this->origin->enter($credentials);
	}

	public function exit(): User {
		return $this->origin->exit();
	}

	/**
	 * Is the user verified?
	 * @param string $email
	 * @return bool
	 */
	private function verified(string $email): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT 1
            FROM verification_codes  
			WHERE user_id = (
				SELECT id
				FROM users
				WHERE email IS NOT DISTINCT FROM ?
			) AND used = TRUE',
			[$email]
		))->field();
	}
}