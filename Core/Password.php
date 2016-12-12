<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface Password {
	/**
	 * Change password to the new given one
	 * @param string $password
	 * @throws \UnexpectedValueException
	 * @return void
	 */
    public function change(string $password): void;
}