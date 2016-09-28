<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface VerificationCode {
	/**
	 * Use the verification code
	 * @throws \Exception
	 * @return void
	 */
	public function use();

	/**
     * Owner of the verification code
     * @return User
	 * @throws \Exception
	 */
	public function owner(): User;
}
