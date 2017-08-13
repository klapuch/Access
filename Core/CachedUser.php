<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Cached user
 */
final class CachedUser implements User {
	private $origin;
	private $id;
	private $properties;

	public function __construct(User $origin) {
		$this->origin = $origin;
	}

	public function id(): string {
		if ($this->id === null)
			$this->id = $this->origin->id();
		return $this->id;
	}

	public function properties(): array {
		if ($this->properties === null)
			$this->properties = $this->origin->properties();
		return $this->properties;
	}
}