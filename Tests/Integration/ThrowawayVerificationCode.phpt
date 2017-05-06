<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ThrowawayVerificationCode extends TestCase\Database {
	public function testUsingValidCode() {
		$this->prepareValidCode();
		(new Access\ThrowawayVerificationCode(
			'valid:code',
			$this->database
		))->use();
		$statement = $this->database->prepare(
			"SELECT used
			FROM verification_codes
			WHERE code = 'valid:code'"
		);
		$statement->execute();
		Assert::true($statement->fetchColumn());
	}

	public function testThrowingOnUsingAlreadyActivatedCode() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used, used_at) VALUES
			(2, 'activated:code', TRUE, NOW())"
		);
		Assert::exception(function() {
			(new Access\ThrowawayVerificationCode(
				'activated:code',
				$this->database
			))->use();
		}, \UnexpectedValueException::class, 'Verification code was already used');
		Assert::exception(function() {
			(new Access\ThrowawayVerificationCode(
				'activated:code',
				$this->database
			))->print(new Output\FakeFormat(''));
		}, \UnexpectedValueException::class, 'Verification code was already used');
	}

	public function testPrintingCode() {
		$this->prepareValidCode();
		Assert::same(
			'|code|valid:code|',
			(new Access\ThrowawayVerificationCode(
				'valid:code',
				$this->database
			))->print(new Output\FakeFormat(''))->serialization()
		);
	}

	private function prepareValidCode() {
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('foo@gmail.com', 'password', 'member'),
			('ber@gmail.com', 'password', 'member')"
		);
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used)
			VALUES (1, 'valid:code', FALSE)"
		);
	}

	protected function prepareDatabase(): void {
		$this->purge(['verification_codes', 'users']);
	}
}

(new ThrowawayVerificationCode())->run();