<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Unit;

use Klapuch\Access;
use Tester\Assert;
use Klapuch\Access\TestCase;

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