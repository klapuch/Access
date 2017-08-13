<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Constant user without any roundtrips
 */
final class ConstantUser implements User {
	private const SENSITIVE_COLUMNS = ['id', 'password'];
	private $id;
	private $properties;

	public function __construct(string $id, array $properties) {
		$this->id = $id;
		$this->properties = $properties;
	}

	public function id(): string {
		return $this->id;
	}

	public function properties(): array {
		return array_diff_ukey(
			$this->properties,
			array_flip(self::SENSITIVE_COLUMNS),
			'strcasecmp'
		);
	}
}