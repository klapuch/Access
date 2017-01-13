<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Welcoming entrance accepting verification code
 */
final class WelcomingEntrance implements Entrance {
    private $database;

    public function __construct(\PDO $database) {
        $this->database = $database;
    }

    public function enter(array $credentials): User {
		[$code] = $credentials;
		$id = (int)(new Storage\ParameterizedQuery(
			$this->database,
			'SELECT user_id
			FROM verification_codes
			WHERE code IS NOT DISTINCT FROM ?
			AND used = TRUE',
			[$code]
		))->field();
		return new ConstantUser($id);
    }
}