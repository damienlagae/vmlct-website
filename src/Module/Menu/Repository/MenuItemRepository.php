<?php

declare(strict_types=1);

namespace App\Module\Menu\Repository;

use App\Module\Menu\Entity\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuItem>
 */
final class MenuItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItem::class);
    }

    /**
     * Active items rooted (no parent), ordered by `position`. Children are
     * fetch-joined so the navbar template doesn't trigger N+1 queries.
     *
     * @return list<MenuItem>
     */
    public function findActiveTree(): array
    {
        $roots = $this->createQueryBuilder('m')
            ->leftJoin('m.children', 'c')
            ->leftJoin('m.page', 'p')
            ->addSelect('c', 'p')
            ->where('m.parent IS NULL')
            ->andWhere('m.active = true')
            ->orderBy('m.position', 'ASC')
            ->addOrderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return array_values($roots);
    }

    /**
     * @return list<MenuItem>
     */
    public function findAllOrdered(): array
    {
        $items = $this->createQueryBuilder('m')
            ->leftJoin('m.parent', 'p')
            ->addSelect('p')
            ->orderBy('m.parent', 'ASC')
            ->addOrderBy('m.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return array_values($items);
    }
}
