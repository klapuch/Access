<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Entrance to web
 */
final class WebEntrance implements Entrance {
	private const IDENTIFIER = 'id';
	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	public function enter(array $credentials): User {
		if (isset($credentials[self::IDENTIFIER]))
			return new RegisteredUser($credentials[self::IDENTIFIER], $this->database);
		return new Guest();
	}

	public function exit(): User {
		return new Guest();
	}
}