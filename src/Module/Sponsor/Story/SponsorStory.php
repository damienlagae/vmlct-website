<?php

declare(strict_types=1);

namespace App\Module\Sponsor\Story;

use App\Module\Sponsor\Factory\SponsorFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'sponsors')]
final class SponsorStory extends Story
{
    public function build(): void
    {
        SponsorFactory::createMany(6, fn (int $i): array => [
            'displayOrder' => $i * 10,
        ]);

        SponsorFactory::new()->inactive()->withOrder(999)->create([
            'name' => 'Inactive Sponsor',
        ]);
    }
}
