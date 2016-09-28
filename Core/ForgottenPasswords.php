<?php
declare(strict_types = 1);
namespace Klapuch\Access;

interface ForgottenPasswords {
    /**
     * Remind forgotten password to the user by the given email
     * @param string $email
     * @throws \OverflowException
     * @return void
     */
    public function remind(string $email);
}
