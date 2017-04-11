<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class SessionEntrance extends Tester\TestCase {
	private $sessions = [];

	protected function setUp() {
		parent::setUp();
		Tester\Environment::lock('fs', __DIR__ . '/../Temporary');
		session_start();
	}

	public function testRetrievedUserOnEntering() {
		Assert::equal(
			new Access\FakeUser(1),
			(new Access\SessionEntrance(
				new Access\FakeEntrance(new Access\FakeUser(1)),
				$this->sessions
			))->enter([])
		);
	}

	public function testRetrievedUserOnExiting() {
		Assert::equal(
			new Access\FakeUser(1),
			(new Access\SessionEntrance(
				new Access\FakeEntrance(new Access\FakeUser(1)),
				$this->sessions
			))->exit()
		);
	}

	public function testSettingSession() {
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions
		))->enter([]);
		Assert::same(1, $this->sessions['id']);
	}

	public function testUnSettingSession() {
		$this->sessions['foo'] = 'bar';
		$this->sessions['id'] = 1;
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions
		))->exit();
		Assert::same('bar', $this->sessions['foo']);
		Assert::false(isset($this->sessions['id']));
	}

	public function testRegeneratingSessionOnEnter() {
		$sessionId = session_id();
		(new Access\SessionEntrance(
			new Access\FakeEntrance(new Access\FakeUser(1)),
			$this->sessions
		))->enter([]);
		Assert::notSame(session_id(), $sessionId);
	}
}

(new SessionEntrance())->run();