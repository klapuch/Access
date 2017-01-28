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
	public function testUserWithKnownCode() {
		$user = new Access\ForgetfulUser('foo@bar.cz', $this->database);
        Assert::same(1, $user->id());
		Assert::same(
			['email' => 'foo@bar.cz', 'role' => 'member'],
			$user->properties()
		);
    }

    public function testUserWithUnknownCode() {
		$user = new Access\ForgetfulUser('unknown@bar.cz', $this->database);
        Assert::same(0, $user->id());
        Assert::same([], $user->properties());
    }

    protected function prepareDatabase() {
        $this->purge(['users']);
        $this->database->exec(
            "INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'secret', 'member')"
        );
    }
}

(new ForgetfulUser())->run();