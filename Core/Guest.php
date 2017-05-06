<?php
declare(strict_types = 1);
namespace Klapuch\Access;

final class Guest implements User {
	public function id(): int {
		return 0;
	}

	public function properties(): array {
		return ['role' => 'guest'];
	}
}