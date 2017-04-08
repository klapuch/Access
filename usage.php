<?php
declare(strict_types = 1);
use Klapuch\Access;
use Klapuch\Output;
use Nette\Mail;

/************************************************************************/
/** Registration phase with verification code */
(new Access\SecureVerificationCodes($this->database))->generate('foo@bar.cz');

// Send verification email
(new Access\ReserveVerificationCodes(
	$this->database,
	new Mail\SendmailMailer(),
	(new Mail\Message())->setFrom('FROM')->setSubject('SUBJECT'),
	new Output\XsltTemplate('xsl', new Output\Xml([]))
))->generate('foo@bar.cz');

$verificationCode = new Access\ExistingVerificationCode(
	new Access\ThrowawayVerificationCode('valid:code', $this->database),
	'valid:code',
	$this->database
);
$verificationCode->use();
/************************************************************************/



/************************************************************************/
/** Entering to the system for the first time */
$entrance = new Access\WelcomingEntrance($this->database);
$user = $entrance->enter(['valid:code']);


/** Entering to the system */
$entrance = new Access\VerifiedEntrance(
	$this->database,
	new Access\SecureEntrance($this->database, $cipher)
);
$user = $entrance->enter(['foo@bar.cz', 'secret']);
/************************************************************************/



/************************************************************************/
/** Forgotten password */
(new Access\LimitedForgottenPasswords(
	new Access\SecureForgottenPasswords($database, $cipher),
	$database
))->remind('foo@bar.cz');

// Send an email with the reminder
(new Access\ExpirableRemindedPassword(
	$reminder,
	$this->database,
	new Access\RemindedPassword(
		$reminder,
		$this->database,
		new Access\ThrowawayVerificationCode(
			$reminder,
			$this->database,
			new Access\UserPassword(
				new Access\ForgetfulUser('foo@bar.cz', $this->database),
				$this->database,
				$cipher
			)
		)
	)
))->change('new password');
/************************************************************************/