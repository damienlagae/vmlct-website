<?php

declare(strict_types=1);

namespace App\Module\Programme\Repository;

use App\Module\Programme\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Race>
 */
final class RaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Race::class);
    }

    /**
     * @return list<Race>
     */
    public function findUpcoming(): array
    {
        return array_values($this->createQueryBuilder('r')
            ->andWhere('r.startsAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('r.startsAt', 'ASC')
            ->getQuery()
            ->getResult());
    }

    /**
     * @return list<Race>
     */
    public function findPast(int $limit = 20): array
    {
        return array_values($this->createQueryBuilder('r')
            ->andWhere('r.startsAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('r.startsAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    /**
     * Groups a list of races by their `Y-m` start month, preserving the
     * input order inside each group.
     *
     * @param list<Race> $races
     *
     * @return array<string, list<Race>>
     */
    public static function groupByMonth(array $races): array
    {
        $grouped = [];
        foreach ($races as $race) {
            $key = $race->getStartsAt()->format('Y-m');
            $grouped[$key] ??= [];
            $grouped[$key][] = $race;
        }

        return $grouped;
    }
}
