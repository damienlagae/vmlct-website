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
     * Upcoming = the race's last day (endDate when set, otherwise startDate)
     * is today or later. DQL has no COALESCE so we OR the two cases.
     *
     * @return list<Race>
     */
    public function findUpcoming(): array
    {
        $today = new \DateTimeImmutable('today');

        return array_values($this->createQueryBuilder('r')
            ->andWhere('(r.endDate IS NOT NULL AND r.endDate >= :today) OR (r.endDate IS NULL AND r.startDate >= :today)')
            ->setParameter('today', $today)
            ->orderBy('r.startDate', 'ASC')
            ->getQuery()
            ->getResult());
    }

    /**
     * @return list<Race>
     */
    public function findPast(int $limit = 20): array
    {
        $today = new \DateTimeImmutable('today');

        return array_values($this->createQueryBuilder('r')
            ->andWhere('(r.endDate IS NOT NULL AND r.endDate < :today) OR (r.endDate IS NULL AND r.startDate < :today)')
            ->setParameter('today', $today)
            ->orderBy('r.startDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    /**
     * Groups races by their `Y-m` start month, preserving order.
     *
     * @param list<Race> $races
     *
     * @return array<string, list<Race>>
     */
    public static function groupByMonth(array $races): array
    {
        $grouped = [];
        foreach ($races as $race) {
            $key = $race->getStartDate()->format('Y-m');
            $grouped[$key] ??= [];
            $grouped[$key][] = $race;
        }

        return $grouped;
    }
}
