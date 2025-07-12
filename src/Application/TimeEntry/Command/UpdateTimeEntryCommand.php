<?php

namespace App\Application\TimeEntry\Command;

class UpdateTimeEntryCommand
{

    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly int $duration,
        public readonly string $description,
        public readonly ?string $project = null
    ) {}
}
