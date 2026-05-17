<?php

declare(strict_types=1);

namespace App\Module\Team\Repository;

use App\Module\Team\Entity\Rider;
use App\Module\Team\Entity\RiderCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Rider>
 */
final class RiderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rider::class);
    }

    /**
     * @return list<Rider>
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.active = true')
            ->orderBy('r.category', 'ASC')
            ->addOrderBy('r.lastName', 'ASC')
            ->addOrderBy('r.firstName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<string, list<Rider>> map of category value => list of riders
     */
    public function findActiveGroupedByCategory(): array
    {
        $riders = $this->findActiveOrdered();
        $grouped = [];
        foreach (RiderCategory::ordered() as $category) {
            $grouped[$category->value] = [];
        }
        foreach ($riders as $rider) {
            $grouped[$rider->getCategory()->value][] = $rider;
        }

        return $grouped;
    }
}
