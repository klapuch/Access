<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Tester\Assert;
use Klapuch\Access\TestCase;

require __DIR__ . '/../bootstrap.php';

final class ForgetfulUser extends TestCase\Database {
	public function testUserWithKnownReminder() {
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'secret', 'member')"
		);
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used) VALUES 
			(1, NOW(), 'valid:reminder', FALSE)"
		);
		$user = new Access\ForgetfulUser('valid:reminder', $this->database);
        Assert::same(1, $user->id());
		Assert::same(
			['email' => 'foo@bar.cz', 'role' => 'member'],
			$user->properties()
		);
    }

    public function testNoUserOnInvalidReminder() {
		$user = new Access\ForgetfulUser('invalid:reminder', $this->database);
        Assert::same(0, $user->id());
        Assert::same([], $user->properties());
    }

    protected function prepareDatabase() {
        $this->purge(['users', 'forgotten_passwords']);
    }
}

(new ForgetfulUser())->run();