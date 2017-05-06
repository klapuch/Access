<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface VerificationCodes {
	/**
	 * Generate a new unique verification code for the given email
	 * @param string $email
	 * @return \Klapuch\Access\VerificationCode
	 */
	public function generate(string $email): VerificationCode;
}