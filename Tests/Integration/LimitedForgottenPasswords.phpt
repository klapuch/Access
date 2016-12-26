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

final class LimitedForgottenPasswords extends TestCase\Database {
	/**
	 * @throws \OverflowException You have reached limit 3 forgotten passwords in last 24 hours
	 */
	public function testOversteppedReminding() {
		$this->database->exec(
			"INSERT INTO forgotten_passwords (id, user_id, reminded_at, reminder) VALUES 
			(1, 1, NOW() - INTERVAL '1 HOUR', 'reminder1'),
			(2, 1, NOW() - INTERVAL '2 HOUR', 'reminder2'),
			(3, 1, NOW() - INTERVAL '3 HOUR', 'reminder3')"
		);
		(new Access\LimitedForgottenPasswords(
			new Access\FakeForgottenPasswords,
			$this->database
		))->remind('foo@gmail.com');
	}

	public function testRemindingInAllowedTimeRange() {
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder) VALUES 
			(1, NOW(), 'reminder0'),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder1'),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder2'),
			(1, NOW() - INTERVAL '25 HOUR', 'reminder3'),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder4'),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder5'),
			(1, NOW() - INTERVAL '24 HOUR', 'reminder6'),
			(1, NOW() - INTERVAL '26 HOUR', 'reminder7')"
		);
		Assert::noError(
			function() {
				(new Access\LimitedForgottenPasswords(
					new Access\FakeForgottenPasswords,
					$this->database
				))->remind('foo@gmail.com');
			}
		);
	}

	protected function prepareDatabase() {
		$this->purge(['users', 'forgotten_passwords']);
		$this->database->exec(
			"INSERT INTO users (id, email, password) VALUES
			(1, 'foo@gmail.com', 'password')"
		);
	}
}

(new LimitedForgottenPasswords())->run();