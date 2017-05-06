<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Already registered user in the system
 */
final class RegisteredUser implements User {
	private $id;
	private $database;

	public function __construct(int $id, \PDO $database) {
		$this->id = $id;
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
		return (new ConstantUser($user['id'], $user))->properties();
	}

	public function id(): int {
		if ($this->registered($this->id))
			return $this->id;
		throw new \InvalidArgumentException(
			'The user has not been registered yet'
		);
	}

	private function registered(int $id): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT 1
			FROM users
			WHERE id IS NOT DISTINCT FROM ?',
			[$id]
		))->field();
	}
}