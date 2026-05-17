<?php

declare(strict_types=1);

namespace App\Module\Team\Story;

use App\Module\Team\Entity\RiderCategory;
use App\Module\Team\Entity\StaffRole;
use App\Module\Team\Factory\RiderFactory;
use App\Module\Team\Factory\StaffFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'team')]
final class TeamStory extends Story
{
    public function build(): void
    {
        StaffFactory::new()->withRole(StaffRole::Director)->create();
        StaffFactory::new()->withRole(StaffRole::Coach)->create();
        StaffFactory::new()->withRole(StaffRole::Mechanic)->create();

        foreach (RiderCategory::ordered() as $category) {
            RiderFactory::new()->inCategory($category)->createMany(3);
        }
    }
}
