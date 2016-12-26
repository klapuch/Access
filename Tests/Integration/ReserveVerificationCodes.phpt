<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
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
		Assert::noError(function() {
			(new Access\ReserveVerificationCodes(
				$this->database
			))->generate('foo@bar.cz');
		});
	}

	/**
	 * @throws \Exception For the given email, there is no valid verification code
	 */
	public function testRegeneratingForOnceUsedCode() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used, used_at)
			VALUES (1, '123456', TRUE, NOW())"
		);
        (new Access\ReserveVerificationCodes(
        	$this->database
		))->generate('foo@bar.cz');
	}

	protected function prepareDatabase() {
		$this->purge(['verification_codes', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password) VALUES
			('foo@bar.cz', 'password')"
		);
	}
}

(new ReserveVerificationCodes())->run();