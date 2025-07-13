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

        $entry = TimeEntry::create(
            user: $user,
            date: $command->date,
            startTime: $command->startTime,
            endTime: $command->endTime,
            duration: $command->duration,
            description: $command->description,
            project: $command->project
        );

        $this->timeEntryRepository->save($entry);

        return $entry->getId();
    }
}
