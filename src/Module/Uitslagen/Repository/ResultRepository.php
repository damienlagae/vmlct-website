<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Repository;

use App\Module\Uitslagen\Entity\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Result>
 */
final class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    /**
     * Recent results, most recent first. Sorts on the on-row `raceDate`
     * with a `createdAt` fallback for rows the editor hasn't dated yet.
     * Linked planned races have their `startsAt` mirrored into `raceDate`
     * by the admin form on save, so the ordering stays consistent across
     * standalone and linked results.
     *
     * @return list<Result>
     */
    public function findRecent(int $limit = 50): array
    {
        return array_values($this->createQueryBuilder('r')
            ->leftJoin('r.race', 'race')
            ->leftJoin('r.rider', 'rider')
            ->addSelect('race', 'rider')
            ->orderBy('r.raceDate', 'DESC')
            ->addOrderBy('r.createdAt', 'DESC')
            ->addOrderBy('r.rank', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }
}
