<?php

namespace App\Application\TimeEntry\Handler;

use App\Application\TimeEntry\Command\UpdateTimeEntryCommand;
use App\Domain\TimeEntry\Entity\TimeEntry;
use App\Domain\TimeEntry\Repository\TimeEntryRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateTimeEntryHandler
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository,
        private UserRepositoryInterface $userRepository,
        private TokenStorageInterface $tokenStorage,
    ) {}

    public function __invoke(UpdateTimeEntryCommand $command): int
    {
        $currentUser = $this->tokenStorage->getToken()?->getUser();

        if (!$currentUser) {
            throw new \InvalidArgumentException('User not found');
        }

        $entry = $this->timeEntryRepository->findById($command->id);

        if (!$entry) {
            throw new \InvalidArgumentException('TimeEntry not found');
        }

        if ($currentUser->getId() !== $entry->getUser()->getId()) {
            throw new \InvalidArgumentException('Cannot update this TimeEntry.');
        }

        $entry->setDate(DateTime::createFromFormat('Y-m-d', $command->date));
        $entry->setStartTime(DateTime::createFromFormat('H:i', $command->startTime));
        $entry->setEndTime(DateTime::createFromFormat('H:i', $command->endTime));
        $entry->setProject($command->project);
        $entry->setDescription($command->description);
        $entry->setDuration($command->duration);
        $this->timeEntryRepository->save($entry);

        return $entry->getId();
    }
}
