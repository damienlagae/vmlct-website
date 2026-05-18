<?php

declare(strict_types=1);

namespace App\Page\Story;

use App\Page\Factory\PageFactory;
use App\Shared\Content\Block\HeadingBlock;
use App\Shared\Content\Block\HeadingLevel;
use App\Shared\Content\Block\TextBlock;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'pages')]
final class PageStory extends Story
{
    public function build(): void
    {
        PageFactory::createOne([
            'title' => 'Over ons',
            'path' => 'over',
            'excerpt' => 'Maak kennis met de jeugdwielerploeg uit het Waasland.',
            'publishedAt' => new \DateTimeImmutable('-2 months'),
            'metaTitle' => 'Over Van Moer Logistics Cycling Team',
            'metaDescription' => 'Jeugdwielerclub uit Beveren, actief in wielrennen op de weg, baanwielrennen en veldrijden.',
            'content' => [
                (new HeadingBlock('Onze club', HeadingLevel::H2))->toArray(),
                (new TextBlock('<p>Sinds de oprichting van het Van Moer Logistics Cycling Team hebben al veel jeugdrenners en -rensters met succes in de kleuren van onze club gereden.</p>'))->toArray(),
                (new HeadingBlock('Teamwerk', HeadingLevel::H2))->toArray(),
                (new TextBlock('<p>Ook al lijkt de sport individueel, het is toch een <strong>team</strong> waarin jij ook deelneemt.</p>'))->toArray(),
            ],
        ]);

        PageFactory::createOne([
            'title' => 'Uitrusting',
            'path' => 'uitrusting',
            'excerpt' => 'Alles wat je nodig hebt om te starten.',
            'publishedAt' => new \DateTimeImmutable('-1 month'),
            'content' => [
                (new TextBlock('<p>Een korte gids over de uitrusting voor jonge renners.</p>'))->toArray(),
                (new HeadingBlock('Helm en bril', HeadingLevel::H3))->toArray(),
                (new TextBlock('<p>De helm is verplicht en de bril sterk aangeraden.</p>'))->toArray(),
            ],
        ]);
    }
}
