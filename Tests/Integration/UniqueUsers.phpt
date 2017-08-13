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

final class UniqueUsers extends TestCase\Database {
	public function testRegisteringBrandNewOne() {
		$user = (new Access\UniqueUsers(
			$this->database,
			new Encryption\FakeCipher()
		))->register('foo@bar.cz', 'passw0rt', 'member');
		Assert::same('1', $user->id());
		Assert::same(['email' => 'foo@bar.cz', 'role' => 'member'], $user->properties());
		$statement = $this->database->prepare('SELECT * FROM users');
		$statement->execute();
		$rows = $statement->fetchAll();
		Assert::count(1, $rows);
		Assert::same('foo@bar.cz', $rows[0]['email']);
		Assert::same('secret', $rows[0]['password']);
		Assert::same('member', $rows[0]['role']);
		Assert::same(1, $rows[0]['id']);
	}

	public function testRegisteringMultipleDifferentEmails() {
		$users = new Access\UniqueUsers(
			$this->database,
			new Encryption\FakeCipher()
		);
		$users->register('foo@bar.cz', 'ultra secret password', 'admin');
		$users->register('bar@foo.cz', 'weak password', 'master');
		$statement = $this->database->prepare('SELECT * FROM users');
		$statement->execute();
		$rows = $statement->fetchAll();
		Assert::count(2, $rows);
		Assert::same(1, $rows[0]['id']);
		Assert::same(2, $rows[1]['id']);
	}

	public function testThrowingOnDuplicatedEmail() {
		$register = function() {
			(new Access\UniqueUsers(
				$this->database,
				new Encryption\FakeCipher()
			))->register('foo@bar.cz', 'password', 'member');
		};
		Assert::noError($register);
		$ex = Assert::exception(
			$register,
			\InvalidArgumentException::class,
			'Email "foo@bar.cz" already exists'
		);
		Assert::type(\Throwable::class, $ex->getPrevious());
	}

	public function testThrowingOnDuplicatedCaseInsensitiveEmail() {
		$email = 'foo@bar.cz';
		$register = function(string $email) {
			(new Access\UniqueUsers(
				$this->database,
				new Encryption\FakeCipher()
			))->register($email, 'password', 'member');
		};
		Assert::noError(function() use ($register, $email) {
			$register($email);
		});
		$ex = Assert::exception(
			function() use ($register, $email) {
				$register(strtoupper($email));
			},
			\InvalidArgumentException::class,
			'Email "FOO@BAR.CZ" already exists'
		);
		Assert::type(\Throwable::class, $ex->getPrevious());
	}

	protected function prepareDatabase(): void {
		$this->purge(['users']);
	}
}

(new UniqueUsers())->run();