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

final class ThrowawayVerificationCode extends TestCase\Database {
	public function testUsingYetValidCode() {
		$this->prepareValidCode();
		(new Access\ThrowawayVerificationCode(
			'valid:code',
			$this->database
		))->use();
		Assert::true(
			$this->database->fetchColumn(
				"SELECT used
				FROM verification_codes
				WHERE code = 'valid:code'"
			)
		);
	}

	/**
	 * @throws \Exception Verification code was already used
	 */
	public function testUsingAlreadyActivatedCode() {
		$this->database->query(
			"INSERT INTO verification_codes (user_id, code, used, used_at) VALUES
			(2, 'activated:code', TRUE, NOW())"
		);
		(new Access\ThrowawayVerificationCode(
			'activated:code',
			$this->database
		))->use();
	}

	public function testOwner() {
		$this->prepareValidCode();
		$user = (new Access\ThrowawayVerificationCode(
			'valid:code',
			$this->database
		))->owner();
		Assert::same(1, $user->id());
	}

	private function prepareValidCode() {
		$this->database->query(
			"INSERT INTO users (email, password) VALUES
			('foo@gmail.com', 'password')"
        );
        $this->database->query(
			"INSERT INTO users (email, password) VALUES
			('bar@gmail.com', 'password')"
		);
		$this->database->query(
			"INSERT INTO verification_codes (user_id, code, used)
			VALUES (1, 'valid:code', FALSE)"
		);
	}

	protected function prepareDatabase() {
		$this->purge(['verification_codes', 'users']);
	}
}

(new ThrowawayVerificationCode())->run();
