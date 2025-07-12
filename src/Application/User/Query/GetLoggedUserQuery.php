<?php

namespace App\Application\User\Query;

readonly class GetLoggedUserQuery
{
    public function __construct(
        public string $email
    ) {}
}
