<?php

namespace App\Application\TimeEntry\Handler;

use App\Application\TimeEntry\Command\DeleteTimeEntryCommand;
use App\Domain\TimeEntry\Repository\TimeEntryRepositoryInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteTimeEntryHandler
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository,
        private TokenStorageInterface $tokenStorage
    ) {}

    public function __invoke(DeleteTimeEntryCommand $command): void
    {
        $entry = $this->timeEntryRepository->findById($command->id);

        if (!$entry) {
            throw new NotFoundHttpException("TimeEntry not found");
        }

        $currentUser = $this->tokenStorage->getToken()?->getUser();

        if (!$currentUser || $currentUser->getId() !== $entry->getUser()->getId()) {
            throw new AccessDeniedException("Cannot delete this TimeEntry.");
        }

        $this->timeEntryRepository->remove($entry);
    }
}
