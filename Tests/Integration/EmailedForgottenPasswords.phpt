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
use Nette\Mail;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class EmailedForgottenPasswords extends TestCase\Database {
	public function testRemindingToEmail() {
		$this->database->exec(
			"INSERT INTO forgotten_passwords (user_id, reminded_at, reminder, used) VALUES 
			(1, NOW() - INTERVAL '3 HOUR', 'xxxxx', FALSE),
			(1, NOW(), '123456', FALSE),
			(2, NOW() + INTERVAL '1 HOUR', 'aaaaa', FALSE),
			(2, NOW() + INTERVAL '1 HOUR', 'bbbbb', FALSE),
			(1, NOW() + INTERVAL '1 HOUR', 'zzzzz', TRUE),
			(1, NOW() - INTERVAL '1 HOUR', 'yyyyy', FALSE)"
		);
		ob_start();
		(new Access\EmailedForgottenPasswords(
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
					return sprintf('<REMINDER>%s</REMINDER>', $variables['reminder']);
				}
			}
		))->remind('foo@bar.cz');
		$message = ob_get_clean();
		Assert::contains('foo@bar.cz', $message);
		Assert::contains('<REMINDER>123456</REMINDER>', $message);
	}

	protected function prepareDatabase(): void {
		$this->purge(['users', 'forgotten_passwords']);
		$this->database->exec(
			"INSERT INTO users (id, email, password, role) VALUES
			(1, 'foo@bar.cz', 'password', 'member')"
		);
	}
}

(new EmailedForgottenPasswords())->run();