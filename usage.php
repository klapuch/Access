<?php
declare(strict_types = 1);
use Klapuch\Access;

/************************************************************************/
/** Registration phase with verification code */
(new Access\SecureVerificationCodes($this->database))->generate('foo@bar.cz');

// No email was received?
(new Access\ReserveVerificationCodes($this->database))->generate('foo@bar.cz');

// Send an email with the verfication code

$verificationCode = new Access\ExistingVerificationCode(
	new Access\ThrowawayVerificationCode('valid:code', $this->database),
	'valid:code',
	$this->database
);
$verificationCode->use();
$owner = $verificationCode->owner();
// Log user with the known ID from the owner
/************************************************************************/



/************************************************************************/
/** Entering to the system */
$entrance = new Access\VerifiedEntrance(
	$this->database,
	new Access\SecureEntrance($this->database, $cipher)
);
$user = $entrance->enter(['email' => 'foo@bar.cz', 'password' => 'secret']);
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