<?php

namespace App\Application\User\Dto;

class UserDto
{
    public function __construct(
        public int $id,
        public string $email,
        public string $name,
        public string $role,
        public ?int $managerId = null,
        /** @var UserDto[] */
        public array $teamMembers = []
    ) {}
}
