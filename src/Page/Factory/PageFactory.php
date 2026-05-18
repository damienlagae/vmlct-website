<?php

declare(strict_types=1);

namespace App\Page\Factory;

use App\Page\Entity\Page;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Page>
 */
final class PageFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Page::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        $title = self::faker()->unique()->sentence(3);
        $path = (new AsciiSlugger())->slug($title)->lower()->toString();

        return [
            'title' => $title,
            'path' => $path,
            'excerpt' => self::faker()->paragraph(1),
            'content' => [],
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now')),
        ];
    }

    public function draft(): self
    {
        return $this->with(['publishedAt' => null]);
    }
}
