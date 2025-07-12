<?php

namespace App\Interface\Http\Controller;

use App\Application\User\Command\LoginUserCommand;
use App\Application\User\Query\GetLoggedUserQuery;
use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsController]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly CommandBus            $commandBus,
        private readonly QueryBus              $queryBus,
        private readonly TokenStorageInterface $tokenStorage)
    {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $command = new LoginUserCommand(
            $data['email'] ?? '',
            $data['password'] ?? ''
        );

        $token = $this->commandBus->handle($command);

        return new JsonResponse(['token' => $token]);
    }

    #[Route('/api/logged', name: 'api_logged', methods: ['GET'])]
    public function logged(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user || !\is_object($user)) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $dto = $this->queryBus->handle(new GetLoggedUserQuery($user->getUserIdentifier()));

        return $this->json($dto);
    }
}
