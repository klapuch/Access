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
    public function testIdWithKnownCode() {
        Assert::same(
            1,
            (new Access\ForgetfulUser('foo@bar.cz', $this->database))->id()
        );
    }

    public function testIdWithUnknownCode() {
        Assert::same(
            0,
            (new Access\ForgetfulUser('unknown@bar.cz', $this->database))->id()
        );
    }

    protected function prepareDatabase() {
        $this->purge(['users']);
        $this->database->query(
            "INSERT INTO users (email, password) VALUES
            ('foo@bar.cz', 'secret')"
        );
    }
}

(new ForgetfulUser())->run();
