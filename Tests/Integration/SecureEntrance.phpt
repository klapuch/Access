<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\{
    Access, Encryption
};
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SecureEntrance extends TestCase\Database {
    public function testSuccessfulAuthenticatingWithExactlySameCredentials() {
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true)
		))->enter(['foo@bar.cz', 'heslo']);
        Assert::same(1, $user->id());
	}

	public function testExitingAndBecomingToGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\SecureEntrance(
				$this->database,
				new Encryption\FakeCipher(true)
			))->exit()
		);
	}

	public function testSuccessfulAuthenticatingWithCaseInsensitiveEmail() {
		Assert::noError(function() {
			(new Access\SecureEntrance(
				$this->database,
				new Encryption\FakeCipher(true)
			))->enter(['FOO@bar.cz', 'heslo']);
		});
	}

	public function testAuthenticatingWithoutRehashing() {
		$statement = $this->database->prepare(
			'SELECT password FROM users WHERE id = 1'
		);
		$statement->execute();
        Assert::same('heslo', $statement->fetchColumn());
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true, false)
		))->enter(['foo@bar.cz', 'heslo']);
		Assert::same(1, $user->id());
		$statement->execute();
        Assert::same('heslo', $statement->fetchColumn());
	}

	/**
	 * @throws \Exception Email "unknown@bar.cz" does not exist
	 */
	public function testThrowinOnAuthenticatingWithUnknownEmail() {
		(new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher()
		))->enter(['unknown@bar.cz', 'heslo']);
	}

	/**
	 * @throws \Exception Wrong password
	 */
	public function testThrowinOnAuthenticatingWithWrongPassword() {
		(new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(false)
		))->enter(['foo@bar.cz', '2heslo2']);
	}

	public function testAuthenticatingRehasingPassword() {
		$statement = $this->database->prepare(
			'SELECT password FROM users WHERE id = 1'
		);
		$statement->execute();
        Assert::same('heslo', $statement->fetchColumn());
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true, true)
		))->enter(['foo@bar.cz', 'heslo']);
		Assert::same(1, $user->id());
		$statement->execute();
        Assert::same('secret', $statement->fetchColumn());
	}

	protected function prepareDatabase() {
		$this->purge(['users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('foo@bar.cz', 'heslo', 'member')"
		);
	}
}

(new SecureEntrance())->run();