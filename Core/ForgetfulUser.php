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

	public function __construct(string $email, \PDO $database) {
		$this->email = $email;
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
		return (int)(new Storage\ParameterizedQuery(
			$this->database,
			'SELECT id
			FROM users
			WHERE email IS NOT DISTINCT FROM ?',
			[$this->email]
		))->field();
	}
}