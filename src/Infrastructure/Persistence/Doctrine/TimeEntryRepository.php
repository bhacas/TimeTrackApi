<?php

namespace App\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Domain\TimeEntry\Entity\TimeEntry;
use App\Domain\TimeEntry\Repository\TimeEntryRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class TimeEntryRepository extends ServiceEntityRepository implements TimeEntryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
    }

    public function findById(int $id): ?TimeEntry
    {
        return $this->getEntityManager()->getRepository(TimeEntry::class)->find($id);
    }

    public function findByUserId(int $userId): array
    {
        return $this->getEntityManager()->getRepository(TimeEntry::class)->findBy(['user' => $userId]);
    }

    public function findByUsers(array $userIds): array
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('te')
            ->from(TimeEntry::class, 'te')
            ->where('te.user IN (:userIds)')
            ->setParameter('userIds', $userIds)
            ->getQuery()
            ->getResult();
    }

    public function save(TimeEntry $entry): void
    {
        $this->getEntityManager()->persist($entry);
        $this->getEntityManager()->flush();
    }

    public function remove(TimeEntry $entry): void
    {
        $this->getEntityManager()->remove($entry);
        $this->getEntityManager()->flush();
    }
}
