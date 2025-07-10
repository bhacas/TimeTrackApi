<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserLoggedController extends AbstractController
{
    #[Route('/api/logged', name: 'api_logged_user', methods: ['GET'])]
    public function __invoke(?UserInterface $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'Brak uwierzytelnionego użytkownika.'], 401);
        }

        return $this->json($user, 200, [], ['groups' => ['user:read']]);
    }
}
