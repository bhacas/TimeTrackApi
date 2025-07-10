<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\TimeEntry;

class TimeEntryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder                $qb,
        QueryNameGeneratorInterface $qng,
        string                      $resourceClass,
        ?Operation                  $operation = null,
        array                       $context = []
    ): void
    {
        $this->addWhere($qb, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder                $qb,
        QueryNameGeneratorInterface $qng,
        string                      $resourceClass,
        array                       $identifiers,
        ?Operation                  $operation = null,
        array                       $context = []
    ): void
    {
        $this->addWhere($qb, $resourceClass);
    }

    private function addWhere(QueryBuilder $qb, string $resourceClass): void
    {
        if ($resourceClass !== TimeEntry::class) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $alias = $qb->getRootAliases()[0];

        if (method_exists($user, 'getRole') && $user->getRole() === 'manager') {
            // Pobierz ID członków zespołu + samego managera
            $teamMemberIds = array_map(
                fn($member) => $member->getId(),
                $user->getTeamMembers()->toArray()
            );
            $ids = array_merge([$user->getId()], $teamMemberIds);

            $qb->andWhere("$alias.user IN (:teamIds)")
                ->setParameter('teamIds', $ids);
        } else {
            // Zwykły użytkownik — tylko własne wpisy
            $qb->andWhere("$alias.user = :currentUser")
                ->setParameter('currentUser', $user);
        }
    }
}
