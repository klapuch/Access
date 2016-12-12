<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Fake
 */
final class FakePassword implements Password {
	public function change(string $password): void {
    }
}