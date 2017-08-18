<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Entrance creating tokens
 */
final class TokenEntrance implements Entrance {
	private const FORMAT = ['sid_length' => 60, 'sid_bits_per_character' => 6];
	private $origin;

	public function __construct(Entrance $origin) {
		$this->origin = $origin;
	}

	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		if (session_status() === PHP_SESSION_NONE)
			session_start(self::FORMAT);
		session_regenerate_id(true);
		$_SESSION[self::IDENTIFIER] = $user->id();
		return new ConstantUser(session_id(), $user->properties());
	}

	public function exit(): User {
		return $this->origin->exit();
	}
}