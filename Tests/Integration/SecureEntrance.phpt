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
    public function testAuthenticating() {
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true)
		))->enter(['foo@bar.cz', 'heslo']);
        Assert::same(1, $user->id());
	}

    public function testAuthenticatingWithoutRehashing() {
        Assert::same(
            'heslo',
            $this->database->fetchColumn(
                'SELECT password FROM users WHERE id = 1'
            )
        );
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true, false)
		))->enter(['foo@bar.cz', 'heslo']);
        Assert::same(1, $user->id());
        Assert::same(
            'heslo',
            $this->database->fetchColumn(
                'SELECT password FROM users WHERE id = 1'
            )
        );
	}

	/**
	 * @throws \Exception Email "unknown@bar.cz" does not exist
	 */
	public function testUnknownEmail() {
		(new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher()
		))->enter(['unknown@bar.cz', 'heslo']);
	}

	/**
	 * @throws \Exception Wrong password
	 */
	public function testAuthenticatingWithWrongPassword() {
		(new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(false)
		))->enter(['foo@bar.cz', '2heslo2']);
	}

    public function testRehasingPassword() {
        Assert::same(
            'heslo',
            $this->database->fetchColumn(
                'SELECT password FROM users WHERE id = 1'
            )
        );
		$user = (new Access\SecureEntrance(
            $this->database,
            new Encryption\FakeCipher(true, true)
		))->enter(['foo@bar.cz', 'heslo']);
        Assert::same(1, $user->id());
        Assert::same(
            'secret',
            $this->database->fetchColumn(
                'SELECT password FROM users WHERE id = 1'
            )
        );
	}

	protected function prepareDatabase() {
		$this->purge(['users']);
		$this->database->query(
			"INSERT INTO users (email, password) VALUES
			('foo@bar.cz', 'heslo')"
		);
	}
}

(new SecureEntrance())->run();
