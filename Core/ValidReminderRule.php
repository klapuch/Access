<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;
use Klapuch\Validation;

final class ValidReminderRule implements Validation\Rule {
	private const EXPIRATION = 'PT30M';
	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	public function apply($subject): void {
		if (!$this->satisfied($subject))
			throw new \UnexpectedValueException('Reminder is no longer valid.');
	}

	public function satisfied($subject): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			"SELECT 1
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?
			AND used = FALSE
			AND reminded_at + INTERVAL '1 MINUTE' * ? > NOW()",
			[$subject, (new \DateInterval(self::EXPIRATION))->i]
		))->field();
	}
}