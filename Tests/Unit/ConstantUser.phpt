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

final class ConstantUser extends TestCase\Database {
	public function testPropertiesWithoutSensitiveData() {
		$user = new Access\ConstantUser(
			1, ['id' => 1, 'email' => '@', 'role' => ['master'], 'password' => 'secret']
		);
		Assert::same(['email' => '@', 'role' => ['master']], $user->properties());
	}

	public function testCaseInsensitivePropertiesWithoutSensitiveData() {
		$user = new Access\ConstantUser(
			1, ['Id' => 1, 'EmaiL' => '@', 'RolE' => ['master'], 'PaSSworD' => 'secret']
		);
		Assert::same(['EmaiL' => '@', 'RolE' => ['master']], $user->properties());
	}

	public function testAllSensitiveDataEndingWithEmptyProperties() {
		$user = new Access\ConstantUser(
			1, ['id' => 1, 'password' => 'secret']
		);
		Assert::same([], $user->properties());
	}
}

(new ConstantUser())->run();