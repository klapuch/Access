<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Works just with securely generated codes
 */
final class SecureVerificationCodes implements VerificationCodes {
	private $database;

	public function __construct(\PDO $database) {
		$this->database = $database;
	}

	public function generate(string $email): VerificationCode {
		$code = (new Storage\ParameterizedQuery(
			$this->database,
			'INSERT INTO verification_codes (user_id, code, used)
			VALUES ((SELECT id FROM users WHERE email IS NOT DISTINCT FROM ?), ?, FALSE)
			RETURNING code',
			[$email, bin2hex(random_bytes(25)) . ':' . sha1($email)]
		))->field();
		return new ThrowawayVerificationCode($code, $this->database);
	}
}