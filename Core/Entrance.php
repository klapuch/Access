<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface Entrance {
    /**
     * Let the user in
     * @param array $credentials
     * @throws \Exception
     * @return User
     */
    public function enter(array $credentials): User;
}
