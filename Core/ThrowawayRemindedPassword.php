<?php
declare(strict_types = 1);

namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Reminded password just for one use
 */
final class ThrowawayRemindedPassword implements Password {
	private $reminder;
	private $database;
	private $origin;

	public function __construct(
		string $reminder,
		\PDO $database,
		Password $origin
	) {
		$this->reminder = $reminder;
		$this->database = $database;
		$this->origin = $origin;
	}

	public function change(string $password): void {
		if ($this->used($this->reminder))
			throw new \UnexpectedValueException('The reminder is already used');
		$this->origin->change($password);
	}

	/**
	 * Is the reminder already used?
	 * @param string $reminder
	 * @return bool
	 */
	private function used(string $reminder): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT 1
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?
			AND used = TRUE',
			[$reminder]
		))->field();
	}
}