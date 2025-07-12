<?php

namespace App\Application\User\Query;

use App\Application\User\Dto\UserDto;
use App\Domain\User\Repository\UserRepositoryInterface;

class GetLoggedUserHandler
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function __invoke(GetLoggedUserQuery $query): UserDto
    {
        $user = $this->userRepository->findByEmail($query->email);

        $teamMembers = method_exists($user, 'getTeamMembers') && $user->getTeamMembers()
            ? array_map(
                fn($member) => new UserDto(
                    id: $member->getId(),
                    email: $member->getEmail(),
                    name: method_exists($member, 'getName') ? $member->getName() : null,
                    role: method_exists($member, 'getRole') ? $member->getRole() : null,
                    managerId: null,
                    teamMembers: []
                ),
                $user->getTeamMembers()->toArray()
            ) : [];

        return new UserDto(
            id: $user->getId(),
            email: $user->getEmail(),
            name: method_exists($user, 'getName') ? $user->getName() : null,
            role: method_exists($user, 'getRole') ? $user->getRole() : null,
            managerId: method_exists($user, 'getManager') && $user->getManager() ? $user->getManager()->getId() : null,
            teamMembers: $teamMembers
        );
    }
}
