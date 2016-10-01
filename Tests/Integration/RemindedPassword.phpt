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
    const REMINDER = '123456';

    public function testChangingWithValidReminder() {
        $newPassword = '123456789';
        $password = $this->mock(Access\Password::class);
        $password->shouldReceive('change')->once()->with($newPassword);
		(new Access\RemindedPassword(
            self::REMINDER,
			$this->database,
            $password
		))->change($newPassword);
		Assert::true(
			$this->database->fetchColumn(
				"SELECT used
				FROM forgotten_passwords
                WHERE user_id = 1 AND reminder = ?",
                [self::REMINDER]
			)
		);
    }

    public function testChangingWithUnknownReminder() {
        $newPassword = '123456789';
        $password = $this->mock(Access\Password::class);
        $password->shouldReceive('change')->once()->with($newPassword);
        (new Access\RemindedPassword(
			'unknown:reminder',
			$this->database,
            $password
		))->change($newPassword);
        Assert::count(
            1,
			$this->database->fetchAll(
				"SELECT id FROM forgotten_passwords"
			)
        );
        Assert::false(
			$this->database->fetchColumn(
				"SELECT used
				FROM forgotten_passwords
                WHERE user_id = 1 AND reminder = ?",
                [self::REMINDER]
			)
		);
    }


	protected function prepareDatabase() {
        $this->purge(['forgotten_passwords']);
        $this->database->query(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
            (1, FALSE, ?, NOW())",
            [self::REMINDER]
		);
	}
}

(new RemindedPassword())->run();
