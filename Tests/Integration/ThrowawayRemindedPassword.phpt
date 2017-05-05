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

final class ThrowawayRemindedPassword extends TestCase\Database {
	/**
	 * @throws \UnexpectedValueException The reminder is already used
	 */
	public function testThrowingOnAlreadyUsedReminder() {
		$reminder = 'abc123';
		$statement = $this->database->prepare(
			'INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
            (1, TRUE, ?, NOW())'
		);
		$statement->execute([$reminder]);
		(new Access\ThrowawayRemindedPassword(
			$reminder,
			$this->database,
			new Access\FakePassword()
		))->change('123456789');
	}

	public function testUsingUnusedReminder() {
		$reminder = 'abc123';
		$statement = $this->database->prepare(
			'INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
            (1, FALSE, ?, NOW())'
		);
		$statement->execute([$reminder]);
		Assert::noError(function() use ($reminder) {
			(new Access\ThrowawayRemindedPassword(
				$reminder,
				$this->database,
				new Access\FakePassword()
			))->change('123456789');
		});
	}

	protected function prepareDatabase(): void {
		$this->purge(['forgotten_passwords']);
	}
}

(new ThrowawayRemindedPassword())->run();