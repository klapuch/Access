<?php
declare(strict_types = 1);

namespace Klapuch\Access;

use Klapuch\Output;
use Klapuch\Storage;
use Nette\Mail;

/**
 * Forgotten passwords directly sent to your email
 */
final class EmailedForgottenPasswords implements ForgottenPasswords {
	private $database;
	private $mailer;
	private $message;
	private $template;

	public function __construct(
		\PDO $database,
		Mail\IMailer $mailer,
		Mail\Message $message,
		Output\Template $template
	) {
		$this->database = $database;
		$this->mailer = $mailer;
		$this->message = $message;
		$this->template = $template;
	}

	public function remind(string $email): void {
		$this->mailer->send(
			$this->message
				->addTo($email)
				->setHtmlBody($this->template->render(['reminder' => $this->last($email)]))
		);
	}

	/**
	 * The last generated and usable reminder for the given email
	 * @param string $email
	 * @return string
	 */
	private function last(string $email): string {
		return (new Storage\ParameterizedQuery(
			$this->database,
			'SELECT reminder
			FROM forgotten_passwords
			WHERE user_id = (
				SELECT id
				FROM users
				WHERE email IS NOT DISTINCT FROM ?
			)
			AND used = FALSE
			ORDER BY reminded_at DESC
			LIMIT 1',
			[$email]
		))->field();
	}
}