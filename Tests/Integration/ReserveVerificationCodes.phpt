<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ReserveVerificationCodes extends TestCase\Database {
	public function testRegenerating() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used)
			VALUES (1, '123456', FALSE)"
		);
		Assert::equal(
			new Access\ThrowawayVerificationCode('123456', $this->database),
			(new Access\ReserveVerificationCodes(
				$this->database
			))->generate('foo@bar.cz')
		);
	}

	/**
	 * @throws \Exception For the given email, there is no valid verification code
	 */
	public function testThrowingOnRegeneratingForOnceUsedCode() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used, used_at)
			VALUES (1, '123456', TRUE, NOW())"
		);
		(new Access\ReserveVerificationCodes(
			$this->database
		))->generate('foo@bar.cz');
	}

	protected function prepareDatabase(): void {
		$this->purge(['verification_codes', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('foo@bar.cz', 'password', 'member')"
		);
	}
}

(new ReserveVerificationCodes())->run();