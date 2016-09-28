<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Fake
 */
final class FakeForgottenPasswords implements ForgottenPasswords {
	public function remind(string $email): Password {
		return new FakePassword();
	}
}
