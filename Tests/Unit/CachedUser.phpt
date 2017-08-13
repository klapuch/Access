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

final class CachedUser extends TestCase\Mockery {
	public function testCaching() {
		$user = $this->mock(Access\User::class);
		$user->shouldReceive('id')
			->once()
			->andReturn('3');
		$user->shouldReceive('properties')
			->once()
			->andReturn(['role' => 'master']);
		$cachedUser = new Access\CachedUser($user);
		Assert::same('3', $cachedUser->id());
		Assert::same('3', $cachedUser->id());
		Assert::same(['role' => 'master'], $cachedUser->properties());
		Assert::same(['role' => 'master'], $cachedUser->properties());
	}
}

(new CachedUser())->run();