<?php

declare(strict_types=1);

namespace App\Page\Repository;

use App\Page\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
final class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function findOnePublishedByPath(string $path): ?Page
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.path = :path')
            ->andWhere('p.publishedAt IS NOT NULL')
            ->andWhere('p.publishedAt <= :now')
            ->setParameter('path', $path)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Paginator<Page>
     */
    public function findAllForAdminPaginated(int $page, int $perPage = 20): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.title', 'ASC')
            ->setFirstResult(max(0, ($page - 1) * $perPage))
            ->setMaxResults($perPage)
        ;

        return new Paginator($qb->getQuery(), fetchJoinCollection: false);
    }

    /**
     * @return array{total: int, published: int, draft: int}
     */
    public function adminStats(): array
    {
        $now = new \DateTimeImmutable();
        $rows = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) AS total')
            ->addSelect('SUM(CASE WHEN p.publishedAt IS NOT NULL AND p.publishedAt <= :now THEN 1 ELSE 0 END) AS published')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleResult()
        ;

        $total = (int) $rows['total'];
        $published = (int) $rows['published'];

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $total - $published,
        ];
    }
}
