<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\{
    Access, Encryption
};
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SecureForgottenPasswords extends TestCase\Database {
	public function testReminding() {
		(new Access\SecureForgottenPasswords(
			$this->database
		))->remind('foo@bar.cz');
		Assert::same(
			[
				'user_id' => 1,
				'reminder_length' => 141,
				'used' => false,
			],
			$this->database->fetch(
				'SELECT user_id, LENGTH(reminder) AS reminder_length, used
				FROM forgotten_passwords
				WHERE reminded_at <= NOW()'
			)
		);
	}

	protected function prepareDatabase() {
		$this->purge(['forgotten_passwords', 'users']);
		$this->database->query(
			"INSERT INTO users (email, password) VALUES
			('foo@bar.cz', '123')"
		);
	}
}

(new SecureForgottenPasswords())->run();
