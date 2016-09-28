<?php
declare(strict_types = 1);
namespace Klapuch\Access;

use Klapuch\Storage;

/**
 * Works just with securely generated codes
 */
final class SecureVerificationCodes implements VerificationCodes {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function generate(string $email) {
        $code = bin2hex(random_bytes(25)) . ':' . sha1($email);
        $this->database->query(
            'INSERT INTO verification_codes (user_id, code)
            VALUES ((SELECT id FROM users WHERE email IS NOT DISTINCT FROM ?), ?)',
            [$email, $code]
        );
    }
}
