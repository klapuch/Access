<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Internal;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SessionEntrance extends Tester\TestCase {
	private $sessions = [];

	protected function setUp() {
		parent::setUp();
		Tester\Environment::lock('fs', __DIR__ . '/../Temporary');
	}

	public function testRetrievedUserOnEntering() {
		session_start();
		Assert::equal(
			new Access\FakeUser(1),
			(new Access\SessionEntrance(
				new Access\FakeEntrance(new Access\FakeUser(1)),
				$this->sessions,
				new Internal\IniSetExtension([])
			))->enter([])
		);
	}

	/**
	 * @throws \LogicException You are not logged in
	 */
	public function testThrowingOnExitingWithoutSession() {
		(new Access\SessionEntrance(
			new Access\FakeEntrance(),
			$this->sessions,
			new Internal\IniSetExtension([])
		))->exit();
	}

	public function testSettingSession() {
		session_start();
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions,
			new Internal\IniSetExtension([])
		))->enter([]);
		Assert::same(1, $this->sessions['id']);
	}

	public function testUnSettingIdentifiedSessionOnly() {
		$this->sessions['foo'] = 'bar';
		$this->sessions['id'] = 1;
		$user = (new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions,
			new Internal\IniSetExtension([])
		))->exit();
		Assert::equal(new Access\FakeUser(1), $user);
		Assert::same('bar', $this->sessions['foo']);
		Assert::false(isset($this->sessions['id']));
	}

	public function testRegeneratingSessionOnEnter() {
		session_start();
		$sessionId = session_id();
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions,
			new Internal\IniSetExtension([])
		))->enter([]);
		Assert::notSame(session_id(), $sessionId);
	}

	public function testRegenerationForActiveSession() {
		Assert::noError(function() {
			(new Access\SessionEntrance(
				new Access\FakeEntrance(new Access\FakeUser(1)),
				$this->sessions,
				new Internal\IniSetExtension([])
			))->enter([]);
		});
	}

	public function testKeepingSpecialSessionAfterRegeneration() {
		session_start();
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions,
			new Internal\CookieExtension(['SameSite' => 'strict'])
		))->enter([]);
		Assert::contains('SameSite=strict', headers_list()[4]);
	}
}

(new SessionEntrance())->run();