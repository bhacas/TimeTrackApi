<?php

namespace App\Application\User\Handler;

use App\Application\User\Command\LoginUserCommand;
use App\Domain\User\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager
    ) {}

    public function __invoke(LoginUserCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->email);

        if (!$user) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $command->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        return $this->jwtManager->create($user);
    }
}
