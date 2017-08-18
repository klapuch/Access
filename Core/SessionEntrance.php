<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Internal;

/**
 * Entrance representing HTTP session
 */
final class SessionEntrance implements Entrance {
	private const IDENTIFIER = 'id';
	private $origin;
	private $session;
	private $extension;

	public function __construct(
		Entrance $origin,
		array &$session,
		Internal\Extension $extension
	) {
		$this->origin = $origin;
		$this->session = &$session;
		$this->extension = $extension;
	}

	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		if (session_status() !== PHP_SESSION_NONE)
			session_regenerate_id(true);
		$this->extension->improve();
		$this->session[self::IDENTIFIER] = $user->id();
		return $user;
	}

	public function exit(): User {
		if (!isset($this->session[self::IDENTIFIER]))
			throw new \LogicException('You are not logged in');
		unset($this->session[self::IDENTIFIER]);
		return $this->origin->exit();
	}
}