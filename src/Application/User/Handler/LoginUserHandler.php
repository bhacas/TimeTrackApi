<?php

namespace App\Application\User\Handler;

use App\Application\User\Command\LoginUserCommand;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Security\JWTService;
use App\Shared\Exception\AuthenticationException;

class LoginUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JWTService $jwtService
    ) {}

    public function __invoke(LoginUserCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user || !password_verify($command->password, $user->getPassword())) {
            throw new AuthenticationException('Invalid credentials');
        }

        return $this->jwtService->createToken($user);
    }
}
