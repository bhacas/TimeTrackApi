<?php

namespace App\Application\TimeEntry\Command;

class DeleteTimeEntryCommand
{
    public function __construct(public readonly int $id)
    {
    }
}
