<?php

declare(strict_types=1);

namespace App\Shared\Security\Story;

use App\Shared\Security\Factory\UserFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'users')]
final class UserStory extends Story
{
    public function build(): void
    {
        UserFactory::new()->superAdmin()->withPlainPassword('admin')->create([
            'email' => 'admin@vmlct.local',
            'firstName' => 'Admin',
            'lastName' => 'Demo',
        ]);

        UserFactory::new()->admin()->withPlainPassword('editor')->create([
            'email' => 'editor@vmlct.local',
            'firstName' => 'Editor',
            'lastName' => 'Demo',
        ]);

        UserFactory::new()->withPlainPassword('user')->createMany(2);
    }
}
