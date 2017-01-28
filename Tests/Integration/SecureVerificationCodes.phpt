<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SecureVerificationCodes extends TestCase\Database {
	public function testGenerating() {
		(new Access\SecureVerificationCodes($this->database))
			->generate('fooBarEmail');
		$statement = $this->database->prepare(
			'SELECT LENGTH(code)
			FROM verification_codes
			WHERE user_id = 1'
		);
		$statement->execute();
		Assert::same(91, $statement->fetchColumn());
	}

	protected function prepareDatabase() {
		$this->purge(['verification_codes', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('fooBarEmail', 'password', 'member')"
		);
	}
}

(new SecureVerificationCodes())->run();