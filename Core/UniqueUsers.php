<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\{
	Encryption, Storage
};

/**
 * Collection of unique users
 */
final class UniqueUsers implements Users {
	private $database;
	private $cipher;

	public function __construct(\PDO $database, Encryption\Cipher $cipher) {
		$this->database = $database;
		$this->cipher = $cipher;
	}

	public function register(string $email, string $password, string $role): User {
		try {
			$row = (new Storage\ParameterizedQuery(
				$this->database,
				'INSERT INTO users(email, password, role) VALUES
				(?, ?, ?) RETURNING *',
				[$email, $this->cipher->encrypt($password), $role]
			))->row();
			return new ConstantUser($row['id'], $row);
		} catch(Storage\UniqueConstraint $ex) {
			throw new \InvalidArgumentException(
				sprintf('Email "%s" already exists', $email),
				$ex->getCode(),
				$ex
			);
		}
	}
}