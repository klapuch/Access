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
		Assert::same(
			91,
			$this->database->fetchColumn(
				'SELECT LENGTH(code)
				FROM verification_codes
				WHERE user_id = 1'
			)
		);
	}

	protected function prepareDatabase() {
		$this->purge(['verification_codes', 'users']);
		$this->database->query(
			"INSERT INTO users (email, password) VALUES
			('fooBarEmail', 'password')"
		);
	}
}

(new SecureVerificationCodes())->run();
