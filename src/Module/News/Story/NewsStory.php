<?php

declare(strict_types=1);

namespace App\Module\News\Story;

use App\Module\News\Factory\ArticleFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'news')]
final class NewsStory extends Story
{
    public function build(): void
    {
        ArticleFactory::createMany(6);
        ArticleFactory::new()->draft()->createMany(2);
    }
}
