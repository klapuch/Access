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

final class WebEntrance extends TestCase\Database {
	public function testEnteringWithId() {
		Assert::equal(
			new Access\RegisteredUser('123', $this->database),
			(new Access\WebEntrance($this->database))->enter(['id' => '123'])
		);
	}

	public function testEnteringWithoutIdLeadingToBeGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\WebEntrance($this->database))->enter([])
		);
	}

	public function testExitingBecomingGuest() {
		Assert::equal(
			new Access\Guest(),
			(new Access\WebEntrance($this->database))->exit()
		);
	}
}

(new WebEntrance())->run();