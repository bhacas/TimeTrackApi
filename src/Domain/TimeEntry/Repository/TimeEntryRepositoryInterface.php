<?php

namespace App\Domain\TimeEntry\Repository;

use App\Domain\TimeEntry\Entity\TimeEntry;

interface TimeEntryRepositoryInterface
{
    public function findById(int $id): ?TimeEntry;

    public function findByUserId(int $userId): array;

    public function findByUsers(array $userIds): array;

    public function save(TimeEntry $entry): void;

    public function remove(TimeEntry $entry): void;
}
