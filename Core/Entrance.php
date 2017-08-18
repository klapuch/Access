<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface Entrance {
	public const IDENTIFIER = 'id';
	/**
	 * Let the user in
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Access\User
	 */
	public function enter(array $credentials): User;

	/**
	 * Let the user out
	 * @throws \UnexpectedValueException
	 * @return \Klapuch\Access\User
	 */
	public function exit(): User;
}
