<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ExpirableRemindedPassword extends TestCase\Database {
	private const REMINDER = '123456';

	/**
	 * @throws \UnexpectedValueException The reminder expired
	 */
	public function testThrowinOnOldReminder() {
		$statement = $this->database->prepare(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at, expire_at) VALUES
			(1, FALSE, ?, '2000-01-01', NOW() - INTERVAL '2 HOUR')"
		);
		$statement->execute([self::REMINDER]);
		(new Access\ExpirableRemindedPassword(
			self::REMINDER,
			$this->database,
			new Access\FakePassword()
		))->change('123456789');
	}

	public function testChangingPasswordWithFreshReminder() {
		$statement = $this->database->prepare(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at, expire_at) VALUES
			(1, TRUE, :reminder, NOW(), NOW() + INTERVAL '2 HOUR'),
			(1, FALSE, :reminder, NOW(), NOW() + INTERVAL '2 HOUR')"
		);
		$statement->execute([':reminder' => self::REMINDER]);
		Assert::noError(function() {
			(new Access\ExpirableRemindedPassword(
				self::REMINDER,
				$this->database,
				new Access\FakePassword()
			))->change('123456789');
		});
	}

	public function testPrintingWithExpirationTime() {
		$statement = $this->database->prepare(
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at, expire_at) VALUES
			(1, FALSE, '123456', NOW(), NOW() + INTERVAL '31 MINUTES')"
		);
		$statement->execute();
		Assert::same(
			'|reminder|123456||expiration|30 minutes|',
			(new Access\ExpirableRemindedPassword(
				self::REMINDER,
				$this->database,
				new Access\FakePassword(new Output\FakeFormat('|abc||def|'))
			))->print(new Output\FakeFormat(''))->serialization()
		);
	}

	protected function prepareDatabase(): void {
		$this->purge(['forgotten_passwords']);
	}
}

(new ExpirableRemindedPassword())->run();