<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class TokenEntrance extends Tester\TestCase {
	public function testRetrievedUserSessionIdOnEntering() {
		Assert::match(
			'~^[\w\d,-]{60}$~',
			(new Access\TokenEntrance(
				new Access\FakeEntrance(new Access\FakeUser('1', []))
			))->enter([])->id()
		);
	}

	public function testEnteringWithSetSession() {
		(new Access\TokenEntrance(
			new Access\FakeEntrance(new Access\FakeUser('1', []))
		))->enter([]);
		Assert::same('1', $_SESSION['id']);
	}

	public function testNewIdOnEachEntering() {
		$entrance = new Access\TokenEntrance(
			new Access\FakeEntrance(new Access\FakeUser('1', []))
		);
		Assert::notSame($entrance->enter([])->id(), $entrance->enter([])->id());
	}

	public function testExitingWithDelegation() {
		$user = new Access\FakeUser('1');
		Assert::same(
			$user,
			(new Access\TokenEntrance(
				new Access\FakeEntrance($user)
			))->exit()
		);
	}
}

(new TokenEntrance())->run();