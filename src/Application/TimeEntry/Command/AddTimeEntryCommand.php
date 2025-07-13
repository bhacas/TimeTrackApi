<?php

namespace App\Application\TimeEntry\Command;

use Symfony\Component\Validator\Constraints as Assert;

class AddTimeEntryCommand
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Date]
        public readonly string $date,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d{2}:\d{2}$/', message: 'Start time must be in HH:MM format')]
        public readonly string $startTime,

        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\d{2}:\d{2}$/', message: 'End time must be in HH:MM format')]
        public readonly string $endTime,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $duration,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1)]
        public readonly string $description,

        #[Assert\NotBlank]
        public readonly int $userId,

        #[Assert\Length(min: 1)]
        public readonly ?string $project = null
    ) {}
}
