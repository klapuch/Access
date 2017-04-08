<?php
declare(strict_types = 1);

namespace Klapuch\Access;

use Klapuch\{
	Encryption, Storage
};

/**
 * Secure entrance for entering users to the system
 */
final class SecureEntrance implements Entrance {
	private $database;
	private $cipher;

	public function __construct(\PDO $database, Encryption\Cipher $cipher) {
		$this->database = $database;
		$this->cipher = $cipher;
	}

	public function enter(array $credentials): User {
		[$plainEmail, $plainPassword] = $credentials;
		$user = (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT *
			FROM users  
			WHERE LOWER(email) IS NOT DISTINCT FROM LOWER(?)',
			[$plainEmail]
		))->row();
		if (!$this->exists($user)) {
			throw new \Exception(
				sprintf('Email "%s" does not exist', $plainEmail)
			);
		} elseif (!$this->cipher->decrypted(
			$plainPassword,
			$user['password']
		)
		) {
			throw new \Exception('Wrong password');
		}
		if ($this->cipher->deprecated($user['password']))
			$this->rehash($plainPassword, $user['id']);
		return new ConstantUser($user['id'], $user);
	}

	/**
	 * Does the record exist?
	 * @param array $row
	 * @return bool
	 */
	private function exists(array $row): bool {
		return (bool)$row;
	}

	/**
	 * Rehash the password with the newest one
	 * @param string $password
	 * @param int $id
	 */
	private function rehash(string $password, int $id): void {
		(new Storage\ParameterizedQuery(
			$this->database,
			'UPDATE users
			SET password = ?
			WHERE id IS NOT DISTINCT FROM ?',
			[$this->cipher->encryption($password), $id]
		))->execute();
	}
}