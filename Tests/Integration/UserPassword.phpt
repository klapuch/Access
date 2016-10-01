<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\{
    Access, Encryption
};
use Tester\Assert;
use Klapuch\Access\TestCase;

require __DIR__ . '/../bootstrap.php';

final class UserPassword extends TestCase\Database {
    public function testChangingWithHashing() {
        (new Access\UserPassword(
			new Access\FakeUser(1),
            $this->database,
            new Encryption\FakeCipher()
        ))->change('willBeEncrypted');
        $user = $this->database->fetch(
            'SELECT email, password FROM users WHERE id = 1'
        );
        Assert::same('secret', $user['password']);
    }

    public function testChangingWithoutAffectingOthers() {
        (new Access\UserPassword(
        	new Access\FakeUser(1),
            $this->database,
            new Encryption\FakeCipher()
        ))->change('willBeEncrypted');
        $users = $this->database->fetchAll(
            'SELECT id, password FROM users'
        );
        Assert::count(2, $users);
        Assert::same(2, $users[0]['id']);
        Assert::same(1, $users[1]['id']);
        Assert::same('pass', $users[0]['password']);        
        Assert::same('secret', $users[1]['password']);        
    }

    protected function prepareDatabase() {
        $this->purge(['users']);
        $this->database->query(
            "INSERT INTO users (email, password) VALUES
            ('foo@bar.cz', 'pass'),
            ('bar@foo.cz', 'pass')"
        );
    }
}

(new UserPassword())->run();
