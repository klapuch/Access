<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Tester\Assert;
use Klapuch\Access\TestCase;

require __DIR__ . '/../bootstrap.php';

final class ExistingVerificationCode extends TestCase\Database {
	/**
	 * @throws \Exception The verification code does not exist
	 */
	public function testUsingUnknownCode() {
		(new Access\ExistingVerificationCode(
			new Access\FakeVerificationCode(),
			'unknown:code',
			$this->database
		))->use();
	}

	public function testUsingKnownCode() {
		$this->prepareCode();
		Assert::noError(
			function() {
				(new Access\ExistingVerificationCode(
					new Access\FakeVerificationCode(),
					'valid:code',
					$this->database
				))->use();
			}
		);
	}

	/**
	 * @throws \Exception The verification code does not exist
	 */
	public function testThrowingOnUsingCaseInsensitiveCode() {
		$this->prepareCode();
		(new Access\ExistingVerificationCode(
			new Access\FakeVerificationCode(),
			'VALID:code',
			$this->database
		))->use();
	}

    protected function prepareDatabase() {
        $this->purge(['verification_codes', 'users']);
        $this->database->exec(
            "INSERT INTO users (email, password) VALUES
            ('foo@bar.cz', 'secret')"
        );
    }

    private function prepareCode() {
        $this->database->exec(
            "INSERT INTO verification_codes (user_id, code, used) VALUES
            (1, 'valid:code', FALSE)"
        );
    }
}

(new ExistingVerificationCode())->run();