<?php
declare(strict_types = 1);
namespace Klapuch\Access;

final class FakeVerificationCode implements VerificationCode {
	private $owner;

	public function __construct(User $owner = null) {
	    $this->owner = $owner;
	}

	public function use(): void {

	}
}