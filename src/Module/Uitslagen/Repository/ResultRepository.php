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
     * Recent results, most recent race first then stage / rank ascending.
     *
     * @return list<Result>
     */
    public function findRecent(int $limit = 200): array
    {
        return array_values($this->createQueryBuilder('r')
            ->innerJoin('r.race', 'race')
            ->innerJoin('r.rider', 'rider')
            ->addSelect('race', 'rider')
            ->orderBy('race.startDate', 'DESC')
            ->addOrderBy('r.stageNumber', 'ASC')
            ->addOrderBy('r.rank', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    /**
     * Groups results by race + stageNumber so a multi-stage race produces
     * one group per stage. Single-day races collapse into one group with
     * stage = null. Preserves the caller-side ordering.
     *
     * Key: `<raceId>|<stageNumber|''>`.
     *
     * @param list<Result> $results
     *
     * @return array<string, list<Result>>
     */
    public static function groupByRaceStage(array $results): array
    {
        $grouped = [];
        foreach ($results as $result) {
            $key = $result->getRace()->getId().'|'.($result->getStageNumber() ?? '');
            $grouped[$key] ??= [];
            $grouped[$key][] = $result;
        }

        return $grouped;
    }
}
