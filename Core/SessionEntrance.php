<?php
declare(strict_types = 1);

namespace Klapuch\Access;

/**
 * Entrance representing HTTP session
 */
final class SessionEntrance implements Entrance {
	private const IDENTIFIER = 'id';
	private $origin;
	private $session;

	public function __construct(Entrance $origin, array &$session) {
		$this->origin = $origin;
		$this->session = &$session;
	}

	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		session_regenerate_id(true);
		$this->session[self::IDENTIFIER] = $user->id();
		return $user;
	}

	public function exit(): User {
		unset($this->session[self::IDENTIFIER]);
		return $this->origin->exit();
	}
}