<?php

namespace App\Application\TimeEntry\Command;

class AddTimeEntryCommand
{

    public function __construct(
        public readonly int $userId,
        public readonly string $date,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly int $duration,
        public readonly string $description,
        public readonly ?string $project = null
    ) {}
}
