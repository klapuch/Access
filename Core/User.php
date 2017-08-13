<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface User {
	/**
	 * ID of the user
	 * @return string
	 */
	public function id(): string;

	/**
	 * Properties of the user such as email, role, username, etc.
	 * @return array
	 */
	public function properties(): array;
}