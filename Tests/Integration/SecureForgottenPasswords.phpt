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

final class SecureForgottenPasswords extends TestCase\Database {
	public function testReminding() {
		(new Access\SecureForgottenPasswords(
			$this->database
		))->remind('foo@bar.cz');
		$statement = $this->database->prepare(
			'SELECT user_id, LENGTH(reminder) AS reminder_length, used
			FROM forgotten_passwords
			WHERE reminded_at <= NOW()'
		);
		$statement->execute();
		Assert::same(
			[
				'user_id' => 1,
				'reminder_length' => 141,
				'used' => false,
			],
			$statement->fetch()
		);
	}

	/**
	 * @throws \UnexpectedValueException The email does not exist
	 */
	public function testThrowingOnUnknownEmail() {
		(new Access\SecureForgottenPasswords(
			$this->database
		))->remind('zzz@zzz.cz');
	}

	public function testPassingWithCaseInsensitiveEmail() {
		Assert::noError(function() {
			(new Access\SecureForgottenPasswords(
				$this->database
			))->remind('FOO@bar.cz');
		});
		$this->purge(['forgotten_passwords', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('FOO@bar.cz', '123', 'member')"
		);
		Assert::noError(function() {
			(new Access\SecureForgottenPasswords(
				$this->database
			))->remind('foo@bar.cz');
		});
	}

	protected function prepareDatabase() {
		$this->purge(['forgotten_passwords', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('foo@bar.cz', '123', 'member')"
		);
	}
}

(new SecureForgottenPasswords())->run();