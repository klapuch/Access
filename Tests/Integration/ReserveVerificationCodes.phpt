<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Klapuch\Output;
use Nette\Mail;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class ReserveVerificationCodes extends TestCase\Database {
	public function testRegenerating() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used)
			VALUES (1, '123456', FALSE)"
		);
		ob_start();
		(new Access\ReserveVerificationCodes(
			$this->database,
			new class implements Mail\IMailer {
				function send(Mail\Message $mail) {
					printf(
						'To: %s',
						implode(array_keys($mail->getHeader('To')))
					);
					printf('Body: %s', $mail->getHtmlBody());
				}
			},
			new Mail\Message(),
			new class implements Output\Template {
				public function render(array $variables = []): string {
					return sprintf('<CODE>%s</CODE>', $variables['code']);
				}
			}
		))->generate('foo@bar.cz');
		$message = ob_get_clean();
		Assert::contains('foo@bar.cz', $message);
		Assert::contains('<CODE>123456</CODE>', $message);
	}

	/**
	 * @throws \Exception For the given email, there is no valid verification code
	 */
	public function testThrowingOnRegeneratingForOnceUsedCode() {
		$this->database->exec(
			"INSERT INTO verification_codes (user_id, code, used, used_at)
			VALUES (1, '123456', TRUE, NOW())"
		);
        (new Access\ReserveVerificationCodes(
        	$this->database,
			new class implements Mail\IMailer {
				function send(Mail\Message $mail) {
				}
			},
			new Mail\Message(),
			new class implements Output\Template {
				public function render(array $variables = []): string {
				}
			}
		))->generate('foo@bar.cz');
	}

	protected function prepareDatabase() {
		$this->purge(['verification_codes', 'users']);
		$this->database->exec(
			"INSERT INTO users (email, password, role) VALUES
			('foo@bar.cz', 'password', 'member')"
		);
	}
}

(new ReserveVerificationCodes())->run();