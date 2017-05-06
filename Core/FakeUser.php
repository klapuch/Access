<?php
declare(strict_types = 1);
namespace Klapuch\Access;

final class FakeUser implements User {
	private $id;
	private $properties;

	public function __construct(int $id = null, array $properties = null) {
		$this->id = $id;
		$this->properties = $properties;
	}

	public function id(): int {
		return $this->id;
	}

	public function properties(): array {
		return $this->properties;
	}
}