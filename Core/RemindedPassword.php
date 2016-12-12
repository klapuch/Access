<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Reminded password
 */
final class RemindedPassword implements Password {
    private $reminder;
    private $database;
    private $origin;

    public function __construct(
        string $reminder,
        Storage\Database $database,
        Password $origin
    ) {
        $this->reminder = $reminder;
        $this->database = $database;
        $this->origin = $origin;
    }

	public function change(string $password) {
		if(!$this->exists($this->reminder))
			throw new \UnexpectedValueException('The reminder does not exist');
        (new Storage\PostgresTransaction($this->database))->start(
            function() use($password) {
                $this->origin->change($password);
                $this->database->query(
                    'UPDATE forgotten_passwords
                    SET used = TRUE
                    WHERE reminder IS NOT DISTINCT FROM ?',
                    [$this->reminder]
                );
            }
        );
	}

	/**
	 * Does the reminder exist?
	 * @param string $reminder
	 * @return bool
	 */
	private function exists(string $reminder): bool {
		return (bool)$this->database->fetchColumn(
			'SELECT 1
			FROM forgotten_passwords
			WHERE reminder IS NOT DISTINCT FROM ?',
			[$reminder]
		);
	}
}