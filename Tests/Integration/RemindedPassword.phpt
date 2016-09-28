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
    public function testChanging() {
        $newPassword = '123456789';
        $password = $this->mock(Access\Password::class);
        $password->shouldReceive('change')->once()->with($newPassword);
		$this->database->query(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
			(1, FALSE, '123456', NOW())"
		);
		(new Access\RemindedPassword(
			'123456',
			$this->database,
            $password
		))->change($newPassword);
		Assert::true(
			$this->database->fetchColumn(
				"SELECT used
				FROM forgotten_passwords
				WHERE user_id = 1 AND reminder = '123456'"
			)
		);
    }

	protected function prepareDatabase() {
		$this->purge(['forgotten_passwords']);
	}
}

(new RemindedPassword())->run();
