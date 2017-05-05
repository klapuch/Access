<?php
declare(strict_types = 1);

namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Forgetful user is the one who forget password
 */
final class ForgetfulUser implements User {
	private $reminder;
	private $database;

	public function __construct(string $reminder, \PDO $database) {
		$this->reminder = $reminder;
		$this->database = $database;
	}

	public function properties(): array {
		$user = (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT *
			FROM users
			WHERE id IS NOT DISTINCT FROM ?',
			[$this->id()]
		))->row();
		return (new ConstantUser($user['id'] ?? 0, $user))->properties();
	}

	public function id(): int {
		return (int) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT id
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?',
			[$this->reminder]
		))->field();
	}
}