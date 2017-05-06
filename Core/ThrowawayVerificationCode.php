<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Output;
use Klapuch\Storage;

/**
 * Verification code which can be used just once
 */
final class ThrowawayVerificationCode implements VerificationCode {
	private $code;
	private $database;

	public function __construct(string $code, \PDO $database) {
		$this->code = $code;
		$this->database = $database;
	}

	public function use(): void {
		if ($this->used())
			throw new \UnexpectedValueException('Verification code was already used');
		(new Storage\ParameterizedQuery(
			$this->database,
			'UPDATE verification_codes
            SET used = TRUE, used_at = NOW()
            WHERE code IS NOT DISTINCT FROM ?',
			[$this->code]
		))->execute();
	}

	/**
	 * Was the verification code already used?
	 * @return bool
	 */
	private function used(): bool {
		return (bool) (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT 1
            FROM verification_codes
            WHERE code IS NOT DISTINCT FROM ?
			AND used = TRUE',
			[$this->code]
		))->field();
	}

	public function print(Output\Format $format): Output\Format {
		if ($this->used())
			throw new \UnexpectedValueException('Verification code was already used');
		return $format->with('code', $this->code);
	}
}