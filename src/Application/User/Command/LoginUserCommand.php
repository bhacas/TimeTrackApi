<?php

namespace App\Application\User\Command;

readonly class LoginUserCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
