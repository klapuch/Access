<?php
declare(strict_types = 1);
/**
 * @testCase
 * @phpVersion > 7.1
 */
namespace Klapuch\Access\Unit;

use Klapuch\Access;
use Klapuch\Access\TestCase;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

final class Guest extends TestCase\Mockery {
	public function testStaticId() {
		Assert::same(0, (new Access\Guest())->id());
	}

	public function testStaticProperties() {
		Assert::same(['role' => 'guest'], (new Access\Guest())->properties());
	}
}

(new Guest())->run();