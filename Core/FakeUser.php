<?php
declare(strict_types = 1);
namespace Klapuch\Access;

final class FakeUser implements User {
    private $id;

    public function __construct(int $id = null) {
        $this->id = $id;
    }

    public function id(): int {
        return $this->id;
    }
}