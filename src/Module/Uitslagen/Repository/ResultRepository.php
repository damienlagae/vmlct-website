<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Repository;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceStage;
use App\Module\Team\Entity\Rider;
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
     * Recent results, most recent race first, then stage position, then rank.
     *
     * @return list<Result>
     */
    public function findRecent(int $limit = 200): array
    {
        return array_values($this->createQueryBuilder('r')
            ->innerJoin('r.stage', 'stage')
            ->innerJoin('stage.race', 'race')
            ->innerJoin('r.rider', 'rider')
            ->addSelect('stage', 'race', 'rider')
            ->orderBy('race.startDate', 'DESC')
            ->addOrderBy('stage.position', 'ASC')
            ->addOrderBy('r.rank', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    /**
     * Groups results by stage, preserving input order.
     *
     * @param list<Result> $results
     *
     * @return array<string, list<Result>>
     */
    public static function groupByStage(array $results): array
    {
        $grouped = [];
        foreach ($results as $result) {
            $key = (string) $result->getStage()->getId();
            $grouped[$key] ??= [];
            $grouped[$key][] = $result;
        }

        return $grouped;
    }

    /**
     * Distinct riders who have at least one Result on the given race.
     * Used by the wizard to pre-fill the rider list when adding a new
     * stage to a race that already has results.
     *
     * @return list<Rider>
     */
    public function findRidersOfRace(Race $race): array
    {
        $riderIds = $this->createQueryBuilder('r')
            ->select('DISTINCT IDENTITY(r.rider) AS rider_id')
            ->innerJoin('r.stage', 'stage')
            ->andWhere('stage.race = :race')
            ->setParameter('race', $race)
            ->getQuery()
            ->getSingleColumnResult()
        ;

        if ([] === $riderIds) {
            return [];
        }

        return array_values($this->getEntityManager()
            ->getRepository(Rider::class)
            ->createQueryBuilder('rider')
            ->andWhere('rider.id IN (:ids)')
            ->setParameter('ids', $riderIds)
            ->orderBy('rider.lastName', 'ASC')
            ->addOrderBy('rider.firstName', 'ASC')
            ->getQuery()
            ->getResult());
    }

    public function hasResultsForRace(Race $race): bool
    {
        $count = (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->innerJoin('r.stage', 'stage')
            ->andWhere('stage.race = :race')
            ->setParameter('race', $race)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @return list<Result>
     */
    public function findForStage(RaceStage $stage): array
    {
        return array_values($this->createQueryBuilder('r')
            ->andWhere('r.stage = :stage')
            ->setParameter('stage', $stage)
            ->orderBy('r.rank', 'ASC')
            ->getQuery()
            ->getResult());
    }
}
