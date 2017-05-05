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

final class ValidReminderRule extends TestCase\Database {
	public function testInvalidAsUnknownReminder() {
		$rule = new Access\ValidReminderRule($this->database);
		$reminder = 'abc123';
		Assert::exception(function() use ($rule, $reminder) {
			$rule->apply($reminder);
		}, \UnexpectedValueException::class, 'Reminder is no longer valid.');
		Assert::false($rule->satisfied($reminder));
	}

	public function testInvalidAsUsedReminder() {
		$rule = new Access\ValidReminderRule($this->database);
		$reminder = 'abc123';
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used) VALUES 
			(1, NOW(), 'abc123', TRUE)"
		);
		Assert::exception(function() use ($rule, $reminder) {
			$rule->apply($reminder);
		}, \UnexpectedValueException::class, 'Reminder is no longer valid.');
		Assert::false($rule->satisfied($reminder));
	}

	public function testInvalidAsExpiredReminder() {
		$rule = new Access\ValidReminderRule($this->database);
		$reminder = 'abc123';
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used) VALUES 
			(1, NOW() - INTERVAL '12 HOUR', 'abc123', FALSE)"
		);
		Assert::exception(function() use ($rule, $reminder) {
			$rule->apply($reminder);
		}, \UnexpectedValueException::class, 'Reminder is no longer valid.');
		Assert::false($rule->satisfied($reminder));
	}

	public function testValidReminder() {
		$rule = new Access\ValidReminderRule($this->database);
		$reminder = 'abc123';
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used) VALUES 
			(1, NOW() - INTERVAL '1 MINUTE', 'abc123', FALSE)"
		);
		Assert::noError(function() use ($rule, $reminder) {
			$rule->apply($reminder);
		});
		Assert::true($rule->satisfied($reminder));
	}

	protected function prepareDatabase(): void {
		$this->purge(['forgotten_passwords']);
	}
}

(new ValidReminderRule())->run();