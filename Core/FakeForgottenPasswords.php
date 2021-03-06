<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Fake
 */
final class FakeForgottenPasswords implements ForgottenPasswords {
	private $password;

	public function __construct(Password $password = null) {
		$this->password = $password;
	}

	public function remind(string $email): Password {
		return $this->password;
	}
}