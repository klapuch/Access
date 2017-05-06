<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Output;
use Klapuch\Storage;

/**
 * Reminded password with expiration
 */
final class ExpirableRemindedPassword implements Password {
	private const EXPIRATION = 'PT30M';
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
			"SELECT 1
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?
			AND reminded_at + INTERVAL '1 MINUTE' * ? < NOW()",
			[$reminder, (new \DateInterval(self::EXPIRATION))->i]
		))->field();
	}

	public function print(Output\Format $format): Output\Format {
		return $format->with('reminder', $this->reminder)
			->with(
				'expiration',
				sprintf(
					'%d minutes',
					(new \DateInterval(self::EXPIRATION))->i
				)
			);
	}
}