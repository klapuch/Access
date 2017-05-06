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
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
			(1, FALSE, ?, '2000-01-01')"
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
			"INSERT INTO forgotten_passwords (user_id, used, reminder, reminded_at) VALUES
			(1, FALSE, :reminder, NOW() - INTERVAL '10 MINUTE'),
			(1, TRUE, :reminder, NOW() - INTERVAL '10 MINUTE'),
			(1, FALSE, :reminder, NOW() - INTERVAL '20 MINUTE')"
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
		Assert::same(
			'|abc||def||reminder|123reminder123||expiration|30 minutes|',
			(new Access\ExpirableRemindedPassword(
				'123reminder123',
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