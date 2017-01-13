<?php
/**
 * @testCase
 * @phpVersion > 7.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class VerifiedEntrance extends TestCase\Database {
	/**
	 * @throws \Exception Email has not been verified yet
	 */
    public function testThrowingOnNotVerifiedEmail() {
		(new Access\VerifiedEntrance(
            $this->database,
			new Access\FakeEntrance(new Access\FakeUser())
		))->enter(['unverified@bar.cz', 'heslo']);
	}

	public function testCaseInsensitiveVerifiedEmail() {
		$user = new Access\FakeUser(1);
		Assert::same(
			$user,
			(new Access\VerifiedEntrance(
				$this->database,
				new Access\FakeEntrance($user)
			))->enter(['VERIFIED@bar.cz', 'heslo'])
		);
	}

	protected function prepareDatabase() {
		$this->purge(['users', 'verification_codes']);
		$this->database->exec(
			"INSERT INTO users (email, password) VALUES
			('verified@bar.cz', 'heslo'), ('unverified@bar.cz', 'heslo')"
		);
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used) VALUES
			(1, 'foo', TRUE), (2, 'bar', FALSE)"
		);
	}
}

(new VerifiedEntrance())->run();