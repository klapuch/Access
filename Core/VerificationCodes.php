<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface VerificationCodes {
    /**
     * Generate a new unique verification code for the given email
     * @param string $email
	 * @return void
     */
    public function generate(string $email): void;
}