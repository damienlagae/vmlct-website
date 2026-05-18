<?php

declare(strict_types=1);

namespace App\Module\Programme\Repository;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceStage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RaceStage>
 */
final class RaceStageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaceStage::class);
    }

    /**
     * @return list<RaceStage>
     */
    public function findForRace(Race $race): array
    {
        return array_values($this->createQueryBuilder('s')
            ->andWhere('s.race = :race')
            ->setParameter('race', $race)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult());
    }

    public function nextPositionFor(Race $race): int
    {
        $max = $this->createQueryBuilder('s')
            ->select('MAX(s.position)')
            ->andWhere('s.race = :race')
            ->setParameter('race', $race)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return ((int) $max) + 1;
    }
}
