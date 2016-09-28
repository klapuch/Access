<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface User {
    /**
     * ID of the user
     * @return int
     */
    public function id(): int;
}
