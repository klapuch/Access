<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface User {
    /**
     * ID of the user
     * @return int
     */
    public function id(): int;

	/**
	 * Properties of the user such as email, role, username, etc.
	 * @return array
	 */
	public function properties(): array;
}