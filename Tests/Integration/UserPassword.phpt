<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Klapuch\Encryption;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class UserPassword extends TestCase\Database {
	public function testChangingWithHashing() {
		(new Access\UserPassword(
			new Access\FakeUser(1),
			$this->database,
			new Encryption\FakeCipher()
		))->change('willBeEncrypted');
		$statement = $this->database->prepare(
			'SELECT email, password FROM users WHERE id = 1'
		);
		$statement->execute();
		$user = $statement->fetch();
		Assert::same('secret', $user['password']);
	}

	public function testChangingWithoutAffectingOthers() {
		(new Access\UserPassword(
			new Access\FakeUser(1),
			$this->database,
			new Encryption\FakeCipher()
		))->change('willBeEncrypted');
		$statement = $this->database->prepare(
			'SELECT id, password FROM users'
		);
		$statement->execute();
		$users = $statement->fetchAll();
		Assert::count(2, $users);
		Assert::same(2, $users[0]['id']);
		Assert::same(1, $users[1]['id']);
		Assert::same('pass', $users[0]['password']);
		Assert::same('secret', $users[1]['password']);
	}

	protected function prepareDatabase(): void {
		$this->purge(['users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
            ('foo@bar.cz', 'pass', 'member'),
            ('bar@foo.cz', 'pass', 'member')"
		);
	}
}

(new UserPassword())->run();