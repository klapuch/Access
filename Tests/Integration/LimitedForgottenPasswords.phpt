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

final class LimitedForgottenPasswords extends TestCase\Database {
	/**
	 * @throws \OverflowException You have reached limit 3 forgotten passwords in last 24 hours
	 */
	public function testThrowinOnOversteppedReminding() {
		$this->database->exec(
			"INSERT INTO forgotten_passwords (id, user_id, reminded_at, reminder, used, expire_at) VALUES 
			(1, 1, NOW() - INTERVAL '1 HOUR', 'reminder1', FALSE, NOW()),
			(2, 1, NOW() - INTERVAL '2 HOUR', 'reminder2', FALSE, NOW()),
			(3, 1, NOW() - INTERVAL '3 HOUR', 'reminder3', FALSE, NOW())"
		);
		(new Access\LimitedForgottenPasswords(
			new Access\FakeForgottenPasswords,
			$this->database
		))->remind('foo@gmail.com');
	}

	public function testRemindingInAllowedTimeRange() {
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used, expire_at) VALUES 
			(1, NOW(), 'reminder0', FALSE, NOW()),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder1', FALSE, NOW()),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder2', FALSE, NOW()),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder3', FALSE, NOW()),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder4', FALSE, NOW()),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder5', FALSE, NOW()),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder6', FALSE, NOW()),
			(1, NOW() - INTERVAL '26 HOUR', 'reminder7', FALSE, NOW())"
		);
		Assert::noError(
			function() {
				(new Access\LimitedForgottenPasswords(
					new Access\FakeForgottenPasswords(new Access\FakePassword()),
					$this->database
				))->remind('foo@gmail.com');
			}
		);
	}

	protected function prepareDatabase(): void {
		$this->purge(['users', 'forgotten_passwords']);
		$this->database->exec(
			"INSERT INTO users (id, email, password, role) VALUES
			(1, 'foo@gmail.com', 'password', 'member')"
		);
	}
}

(new LimitedForgottenPasswords())->run();