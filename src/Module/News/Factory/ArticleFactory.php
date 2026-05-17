<?php

declare(strict_types=1);

namespace App\Module\News\Factory;

use App\Module\News\Entity\Article;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Article>
 */
final class ArticleFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Article::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        $title = self::faker()->unique()->sentence(6);
        $slug = (new AsciiSlugger())->slug($title)->lower()->toString();

        return [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => self::faker()->paragraph(2),
            'content' => [],
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now')),
        ];
    }

    public function draft(): self
    {
        return $this->with(['publishedAt' => null]);
    }

    public function publishedAt(\DateTimeImmutable $when): self
    {
        return $this->with(['publishedAt' => $when]);
    }
}
