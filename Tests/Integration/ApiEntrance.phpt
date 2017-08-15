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

final class ApiEntrance extends TestCase\Database {
	public function testEnteringWithValidBearerToken() {
		session_set_save_handler(
			new class implements \SessionHandlerInterface {
				public function close() {
					return true;
				}

				public function destroy($id) {
					return true;
				}

				public function gc($maxLifeTime) {
					return true;
				}

				public function open($path, $name) {
					return true;
				}

				public function read($id) {
					return 'id|s:3:"123";';
				}

				public function write($id, $data) {
					return true;
				}
			},
			true
		);
		Assert::equal(
			new Access\RegisteredUser('123', $this->database),
			(new Access\ApiEntrance(
				$this->database
			))->enter(['authorization' => sprintf('Bearer 0c3da2dd2900adb00f8f231e4484c1b5')])
		);
	}

	public function testNoAuthorizationHeaderLeadingToBeGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\ApiEntrance($this->database))->enter([])
		);
	}

	public function testMissingBearerPartLeadingToBeGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\ApiEntrance($this->database))->enter(['authorization' => 'abc'])
		);
	}

	public function testUnknownTokenLeadingToBeGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\ApiEntrance($this->database))->enter(['authorization' => 'Bearer abcdef'])
		);
	}

	public function testExitBecomingGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\ApiEntrance($this->database))->exit()
		);
	}
}

(new ApiEntrance())->run();