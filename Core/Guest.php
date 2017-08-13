<?php
declare(strict_types = 1);
namespace Klapuch\Access;

final class Guest implements User {
	public function id(): string {
		return '';
	}

	public function properties(): array {
		return ['role' => 'guest'];
	}
}