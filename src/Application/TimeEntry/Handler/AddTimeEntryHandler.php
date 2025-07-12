<?php

namespace App\Application\TimeEntry\Handler;

use App\Application\TimeEntry\Command\AddTimeEntryCommand;
use App\Application\TimeEntry\Dto\TimeEntryDto;
use App\Domain\TimeEntry\Entity\TimeEntry;
use App\Domain\TimeEntry\Repository\TimeEntryRepositoryInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use DateTime;

class AddTimeEntryHandler
{
    public function __construct(
        private TimeEntryRepositoryInterface $timeEntryRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(AddTimeEntryCommand $command): int
    {
        $user = $this->userRepository->findById($command->userId);

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $entry = new TimeEntry();
        $entry->setUser($user);
        $entry->setDescription($command->description);
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
