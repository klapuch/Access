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
}
