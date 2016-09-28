<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Applicant is the one who is in registration phase
 */
final class Applicant implements User {
    private $code;
    private $database;

    public function __construct(string $code, Storage\Database $database) {
        $this->code = $code;
        $this->database = $database;
    }

    public function id(): int {
        return (int)$this->database->fetchColumn(
            'SELECT user_id
            FROM verification_codes
            WHERE code IS NOT DISTINCT FROM ?',
            [$this->code]
        );
    }
}
