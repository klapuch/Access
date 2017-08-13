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

final class ForgetfulUser extends TestCase\Database {
	public function testUserWithKnownReminder() {
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'secret', 'member')"
		);
		$this->database->exec(
			"INSERT INTO forgotten_passwords (id, user_id, reminded_at, reminder, used, expire_at) VALUES 
			(2, 1, NOW(), 'valid:reminder', FALSE, NOW())"
		);
		$user = new Access\ForgetfulUser('valid:reminder', $this->database);
		Assert::same('1', $user->id());
		Assert::same(
			['email' => 'foo@bar.cz', 'role' => 'member'],
			$user->properties()
		);
	}

	public function testNoUserOnInvalidReminder() {
		$user = new Access\ForgetfulUser('invalid:reminder', $this->database);
		Assert::same('0', $user->id());
		Assert::same([], $user->properties());
	}

	protected function prepareDatabase(): void {
		$this->purge(['users', 'forgotten_passwords']);
	}
}

(new ForgetfulUser())->run();