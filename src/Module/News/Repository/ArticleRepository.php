<?php

declare(strict_types=1);

namespace App\Module\News\Repository;

use App\Module\News\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
final class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Paginator<Article>
     */
    public function findPublishedPaginated(int $page, int $perPage = 12): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->orderBy('a.publishedAt', 'DESC')
            ->setParameter('now', new \DateTimeImmutable())
            ->setFirstResult(max(0, ($page - 1) * $perPage))
            ->setMaxResults($perPage)
        ;

        return new Paginator($qb->getQuery(), fetchJoinCollection: false);
    }

    public function findOnePublishedBySlug(string $slug): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.slug = :slug')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->andWhere('a.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
