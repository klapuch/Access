<?php
/**
 * @testCase
 * @phpVersion > 7.0.0
 */
namespace Klapuch\Access\Integration;

use Klapuch\Access;
use Tester\Assert;
use Klapuch\Access\TestCase;

require __DIR__ . '/../bootstrap.php';

final class Applicant extends TestCase\Database {
    public function testIdWithKnownCode() {
        Assert::same(
            1,
            (new Access\Applicant('valid:code', $this->database))->id()
        );
    }

    public function testIdWithUnknownCode() {
        Assert::same(
            0,
            (new Access\Applicant('unknown:code', $this->database))->id()
        );
    }

    protected function prepareDatabase() {
        $this->purge(['verification_codes']);
        $this->database->query(
            "INSERT INTO verification_codes (user_id, code) VALUES
            (1, 'valid:code')"
        );
    }
}

(new Applicant())->run();
