<?php

declare(strict_types=1);

namespace App\Module\News\Story;

use App\Module\News\Factory\ArticleFactory;
use App\Shared\Content\Block\HeadingBlock;
use App\Shared\Content\Block\HeadingLevel;
use App\Shared\Content\Block\ImageAlignment;
use App\Shared\Content\Block\ImageBlock;
use App\Shared\Content\Block\TextBlock;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'news')]
final class NewsStory extends Story
{
    public function build(): void
    {
        ArticleFactory::createMany(6);
        ArticleFactory::new()->draft()->createMany(2);

        ArticleFactory::createOne([
            'title' => 'Voorbeeldartikel met blocks',
            'slug' => 'voorbeeldartikel-met-blocks',
            'excerpt' => 'Een korte demo van de drie eerste blocktypes: text, heading en image.',
            'content' => [
                (new TextBlock('<p>Dit is een <strong>tekstblock</strong> met <em>HTML</em>. Bezoek <a href="https://vmlct.local">onze site</a>.</p>'))->toArray(),
                (new HeadingBlock('Een tussentitel', HeadingLevel::H2))->toArray(),
                (new TextBlock('<p>Een tweede paragraaf onder de tussentitel.</p><ul><li>Punt een</li><li>Punt twee</li></ul>'))->toArray(),
                (new ImageBlock('placeholder.jpg', 'Voorbeeldafbeelding', 'Een voorbeeld onderschrift', ImageAlignment::Full))->toArray(),
            ],
        ]);
    }
}
