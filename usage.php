<?php
declare(strict_types = 1);
use Klapuch\Access;

/************************************************************************/
/** Registration phase with verification code */
(new Access\SecureVerificationCodes($database))->generate('foo@bar.cz');

// No email was received?
(new Access\ReserveVerificationCodes($database))->generate('foo@bar.cz');

// Send an email with the verfication code

$verificationCode = new Access\ExistingVerificationCode(
	new Access\ThrowawayVerificationCode('valid:code', $database),
	'valid:code',
	$database
);
$verificationCode->use();
$owner = $verificationCode->owner();
// Log user with the known ID from the owner
/************************************************************************/



/************************************************************************/
/** Entering to the system */
$entrance = new Access\SecureEntrance($database, $cipher);
$user = $entrance->enter(['email' => 'foo@bar.cz', 'password' => 'secret']);
/************************************************************************/



/************************************************************************/
/** Forgotten password */
(new Access\LimitedForgottenPasswords(
	new Access\SecureForgottenPasswords($database, $cipher),
	$database
))->remind('foo@bar.cz');

// Send an email with the reminder

(new Access\RemindedPassword(
	$reminder,
	$database,
	new Access\UserPassword(
		new Access\ForgetfulUser('foo@bar.cz', $database),
		$database,
		$cipher
	)
))->change('new password');
/************************************************************************/