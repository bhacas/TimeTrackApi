<?php

namespace App\Interface\Http\Controller;

use App\Application\TimeEntry\Command\AddTimeEntryCommand;
use App\Application\TimeEntry\Command\DeleteTimeEntryCommand;
use App\Application\TimeEntry\Command\UpdateTimeEntryCommand;
use App\Application\TimeEntry\Dto\TimeEntryDtoFactory;
use App\Domain\TimeEntry\Repository\TimeEntryRepositoryInterface;
use App\Shared\Bus\CommandBus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class TimeEntryController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly TimeEntryRepositoryInterface $timeEntryRepository,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('/api/time_entries', name: 'get_time_entries', methods: ['GET'])]
    public function get(Request $request): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user || !\is_object($user)) {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        $teamIds = array_map(
            fn($member) => $member->getId(),
            $user->getTeamMembers()->toArray()
        );

        $entries = $this->timeEntryRepository->findByUsers(array_merge([$user->getId()], $teamIds));
        $dtos = array_map([TimeEntryDtoFactory::class, 'createFromEntity'], $entries);

        return new JsonResponse($dtos, 200);
    }


    #[Route('/api/time_entries', name: 'add_time_entry', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user || !\is_object($user)) {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        $command = new AddTimeEntryCommand(
            userId: $user->getId(),
            date: $data['date'] ?? '',
            startTime: $data['startTime'],
            endTime: $data['endTime'],
            duration: isset($data['duration']) ? (int)$data['duration'] : 0,
            description: $data['description'] ?? '',
            project: $data['project'] ?? null
        );

        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => array_map(fn($e) => $e->getPropertyPath() . ': ' . $e->getMessage(), iterator_to_array($errors))
            ], 400);
        }

        $id = $this->commandBus->handle($command);
        $entry = $this->timeEntryRepository->findById($id);
        $dto = TimeEntryDtoFactory::createFromEntity($entry);

        return new JsonResponse($dto, 201);
    }

    #[Route('/api/time_entries/{id}', name: 'update_time_entry', methods: ['PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->toArray();
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user || !\is_object($user)) {
            throw new UnauthorizedHttpException('Unauthorized');
        }

        $command = new UpdateTimeEntryCommand(
            id: $id,
            date: $data['date'] ?? '',
            startTime: $data['startTime'],
            endTime: $data['endTime'],
            duration: isset($data['duration']) ? (int)$data['duration'] : 0,
            description: $data['description'] ?? '',
            project: $data['project'] ?? null
        );

        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            return new JsonResponse([
                'errors' => array_map(fn($e) => $e->getPropertyPath() . ': ' . $e->getMessage(), iterator_to_array($errors))
            ], 400);
        }

        $id = $this->commandBus->handle($command);

        $entry = $this->timeEntryRepository->findById($id);
        $dto = TimeEntryDtoFactory::createFromEntity($entry);

        return new JsonResponse($dto, 200);
    }

    #[Route('/api/time_entries/{id}', name: 'delete_time_entry', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->commandBus->handle(new DeleteTimeEntryCommand($id));

        return new JsonResponse(null, 204);
    }
}
