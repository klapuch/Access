<?php
declare(strict_types = 1);

namespace Klapuch\Access;

interface Users {
	/**
	 * Register a new user by the given email, password and role
	 * @param string $email
	 * @param string $password
	 * @param string $role
	 * @throw \InvalidArgumentException
	 * @return \Klapuch\Access\User
	 */
	public function register(
		string $email,
		string $password,
		string $role
	): User;
}