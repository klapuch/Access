<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Welcoming entrance accepting verification code
 */
final class WelcomingEntrance implements Entrance {
	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	public function enter(array $credentials): User {
		[$code] = $credentials;
		$row = (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT users.*
			FROM verification_codes
			INNER JOIN users ON users.id = user_id
			WHERE code IS NOT DISTINCT FROM ?
			AND used = TRUE',
			[(string) $code]
		))->row();
		return new ConstantUser(strval($row['id'] ?? '0'), $row);
	}

	public function exit(): User {
		return new Guest();
	}
}