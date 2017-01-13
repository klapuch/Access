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

final class RemindedPassword extends TestCase\Database {
    const VALID_REMINDER = 'abc123';

	public function testChangingWithValidReminder() {
		$statement = $this->database->prepare(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
			(1, FALSE, ?, NOW())"
		);
		$statement->execute([self::VALID_REMINDER]);
        $newPassword = '123456789';
        $password = $this->mock(Access\Password::class);
        $password->shouldReceive('change')->once()->with($newPassword);
		(new Access\RemindedPassword(
            self::VALID_REMINDER,
			$this->database,
            $password
		))->change($newPassword);
		$statement = $this->database->prepare(
			"SELECT used
			FROM forgotten_passwords
			WHERE user_id = 1 AND reminder = ?"
		);
		$statement->execute([self::VALID_REMINDER]);
		Assert::true($statement->fetchColumn());
    }

	/**
	 * @throws \UnexpectedValueException The reminder does not exist
	 */
    public function testChangingWithUnknownReminder() {
        (new Access\RemindedPassword(
			'unknown:reminder',
			$this->database,
			new Access\FakePassword()
		))->change('123456789');
	}

	/**
	 * @throws \UnexpectedValueException The reminder does not exist
	 */
    public function testThrowingOnUsingCaseInsensitiveReminder() {
		$statement = $this->database->prepare(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
			(1, FALSE, ?, NOW())"
		);
		$statement->execute([self::VALID_REMINDER]);
        (new Access\RemindedPassword(
			strtoupper(self::VALID_REMINDER),
			$this->database,
			new Access\FakePassword()
		))->change('123456789');
    }

	protected function prepareDatabase() {
		$this->purge(['forgotten_passwords']);
	}
}

(new RemindedPassword())->run();