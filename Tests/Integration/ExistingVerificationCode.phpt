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
			'valid:code',
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
	 * @throws \Exception Nobody owns the verification code
	 */
	public function testCodeWithoutOwner() {
		(new Access\ExistingVerificationCode(
			new Access\FakeVerificationCode(),
			'valid:code',
			$this->database
		))->owner();
	}

	public function testOwnedCode() {
		$this->prepareCode();
		$owner = new Access\FakeUser();
		Assert::same(
			$owner,
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode($owner),
				'valid:code',
				$this->database
			))->owner()
		);
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