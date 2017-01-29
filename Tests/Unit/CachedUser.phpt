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

final class CachedUser extends TestCase\Mockery {
	public function testCaching() {
		$user = $this->mock(Access\User::class);
		$user->shouldReceive('id')
			->once()
			->andReturn(3);
		$user->shouldReceive('properties')
			->once()
			->andReturn(['role' => 'master']);
		$cachedUser = new Access\CachedUser($user);
		Assert::same(3, $cachedUser->id());
		Assert::same(3, $cachedUser->id());
		Assert::same(['role' => 'master'], $cachedUser->properties());
		Assert::same(['role' => 'master'], $cachedUser->properties());
	}
}

(new CachedUser())->run();