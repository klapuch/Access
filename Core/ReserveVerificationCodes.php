<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Reserve verification codes which can be given on demand in case the old one has been lost
 * With the "lost" is meant that the code was not received or occurred other issue
 */
final class ReserveVerificationCodes implements VerificationCodes {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function generate(string $email): void {
        $code = $this->database->fetchColumn(
            'SELECT code
            FROM verification_codes
            WHERE user_id = (
                SELECT id
                FROM users
                WHERE email IS NOT DISTINCT FROM ?
            )
            AND used = FALSE',
            [$email]
        );
        if(!$code) {
            throw new \Exception(
                'For the given email, there is no valid verification code'
            );
        }
    }
}