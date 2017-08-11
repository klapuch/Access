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

final class WelcomingEntrance extends TestCase\Database {
	public function testEnteringWithUsedCode() {
		$user = (new Access\WelcomingEntrance(
			$this->database
		))->enter(['used:code']);
		Assert::same(1, $user->id());
	}

	public function testExitingAndBecomingToGuest() {
		Assert::equal(
			new Access\Guest(),
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

	public function testPassingWithStringObject() {
		Assert::noError(
			function() {
				(new Access\WelcomingEntrance(
					$this->database
				))->enter(
					[new class {
						public function __toString() {
							return '123';
						}
					}]
				);
			}
		);
	}

	protected function prepareDatabase(): void {
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