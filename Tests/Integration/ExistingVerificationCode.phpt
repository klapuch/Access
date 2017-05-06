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

final class ExistingVerificationCode extends TestCase\Database {
	public function testThrowingOnUnknownCode() {
		Assert::exception(function() {
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode(),
				'unknown:code',
				$this->database
			))->use();
		}, \UnexpectedValueException::class, 'The verification code does not exist');
		Assert::exception(function() {
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode(),
				'unknown:code',
				$this->database
			))->print(new Output\FakeFormat(''));
		}, \UnexpectedValueException::class, 'The verification code does not exist');
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

	public function testPrintingCodeWithOrigin() {
		$this->prepareCode();
		Assert::same(
			'|abc|def||code|valid:code|',
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode(new Output\FakeFormat('|abc|def|')),
				'valid:code',
				$this->database
			))->print(new Output\FakeFormat(''))->serialization()
		);
	}

	public function testThrowingOnUsingCaseInsensitiveCode() {
		$this->prepareCode();
		Assert::exception(function() {
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode(),
				'VALID:code',
				$this->database
			))->use();
		}, \UnexpectedValueException::class, 'The verification code does not exist');
		Assert::exception(function() {
			(new Access\ExistingVerificationCode(
				new Access\FakeVerificationCode(),
				'VALID:code',
				$this->database
			))->print(new Output\FakeFormat(''));
		}, \UnexpectedValueException::class, 'The verification code does not exist');
	}

	protected function prepareDatabase(): void {
		$this->purge(['verification_codes', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'secret', 'member')"
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