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

final class WelcomingEntrance extends TestCase\Database {
	public function testEnteringWithUnusedCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['used:code']);
        Assert::equal(new Access\ConstantUser(1), $user);
	}

	public function testNoMatchOnEnteringWithUsedCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['unused:code']);
        Assert::equal(new Access\ConstantUser(0), $user);
	}

	public function testNoMatchOnEnteringWithCaseInsensitiveCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['USED:code']);
        Assert::equal(new Access\ConstantUser(0), $user);
    }

    protected function prepareDatabase() {
        $this->purge(['verification_codes']);
        $this->database->exec(
            "INSERT INTO verification_codes (user_id, code, used) VALUES
            (1, 'used:code', TRUE), (2, 'unused:code', FALSE)"
        );
    }
}

(new WelcomingEntrance())->run();