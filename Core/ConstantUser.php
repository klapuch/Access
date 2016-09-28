<?php
declare(strict_types = 1);
namespace Klapuch\Access;

/**
 * Constant user without any roundtrips
 */
final class ConstantUser implements User {
    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function id(): int {
        return $this->id;
    }
}
