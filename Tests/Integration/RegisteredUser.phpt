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

final class RegisteredUser extends TestCase\Database {
	public function testInfoAboutRegisteredUser() {
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'secret', 'member')"
		);
		$user = new Access\RegisteredUser('1', $this->database);
		Assert::same('1', $user->id());
		Assert::same(
			['email' => 'foo@bar.cz', 'role' => 'member'],
			$user->properties()
		);
	}

	public function testThrowingOnNotRegistedUser() {
		$user = new Access\RegisteredUser('1', $this->database);
		Assert::exception(function() use ($user) {
			$user->id();
		}, \InvalidArgumentException::class, 'The user has not been registered yet');
		Assert::exception(function() use ($user) {
			$user->properties();
		}, \InvalidArgumentException::class, 'The user has not been registered yet');
	}

	protected function prepareDatabase(): void {
		$this->purge(['users']);
	}
}

(new RegisteredUser())->run();