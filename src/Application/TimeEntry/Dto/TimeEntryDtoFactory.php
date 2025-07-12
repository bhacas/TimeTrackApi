<?php

namespace App\Application\TimeEntry\Dto;

use App\Domain\TimeEntry\Entity\TimeEntry;

class TimeEntryDtoFactory
{
    public static function createFromEntity(TimeEntry $entry): TimeEntryDto
    {
        return new TimeEntryDto(
            id: $entry->getId(),
            date: $entry->getDate()->format('Y-m-d'),
            startTime: $entry->getStartTime()->format('H:i'),
            endTime: $entry->getEndTime()?->format('H:i') ?? '',
            duration: $entry->getDuration(),
            description: $entry->getDescription() ?? '',
            project: $entry->getProject(),
            userId: $entry->getUser()->getId()
        );
    }
}
