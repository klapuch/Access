<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\{
    Access, Encryption
};
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ExpirableRemindedPassword extends TestCase\Database {
    const REMINDER = '123456';

	/**
	 * @throws \UnexpectedValueException The reminder expired
	 */
	public function testOldRemindedPassword() {
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

	public function testFreshRemindedPassword() {
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

	protected function prepareDatabase() {
		$this->purge(['forgotten_passwords']);
	}
}

(new ExpirableRemindedPassword())->run();