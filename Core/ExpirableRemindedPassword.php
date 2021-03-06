<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Output;
use Klapuch\Storage;

/**
 * Reminded password with expiration
 */
final class ExpirableRemindedPassword implements Password {
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
		if ($this->expired($this->reminder))
			throw new \UnexpectedValueException('The reminder expired');
		$this->origin->change($password);
	}

	/**
	 * Is the reminded password expired?
	 * @param string $reminder
	 * @return bool
	 */
	private function expired(string $reminder): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT 1
            FROM forgotten_passwords
            WHERE reminder IS NOT DISTINCT FROM ?
            AND expire_at < NOW()',
			[$reminder]
		))->field();
	}

	public function print(Output\Format $format): Output\Format {
		return $format->with('reminder', $this->reminder)
			->with('expiration', $this->expiration($this->reminder));
	}

	private function expiration(string $reminder): string {
		return (new Storage\ParameterizedQuery(
			$this->database,
			"SELECT EXTRACT(MINUTE FROM expire_at - NOW()) || ' minutes'
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?",
			[$reminder]
		))->field();
	}
}