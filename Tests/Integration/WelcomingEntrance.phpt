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
	public function testEnteringWithUsedCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['used:code']);
        Assert::same(1, $user->id());
	}

	public function testExitingAndBecomingToGuest() {
		Assert::equal(
			new Access\ConstantUser(0, ['role' => 'guest']),
			(new Access\WelcomingEntrance(
				$this->database
			))->exit()
		);
	}

	public function testNoMatchOnEnteringWithUnusedCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['unused:code']);
        Assert::same(0, $user->id());
	}

	public function testNoMatchOnEnteringWithCaseInsensitiveCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['USED:code']);
        Assert::same(0, $user->id());
	}

    protected function prepareDatabase() {
        $this->purge(['verification_codes', 'users']);
        $this->database->exec(
            "INSERT INTO verification_codes (user_id, code, used) VALUES
            (1, 'used:code', TRUE), (2, 'unused:code', FALSE)"
		);
        $this->database->exec(
            "INSERT INTO users (id, email, password, role) VALUES
			(1, 'foo@bar.cz', 'secret', 'member'),
			(2, 'known@email.cz', 'secret', 'member')"
        );
    }
}

(new WelcomingEntrance())->run();