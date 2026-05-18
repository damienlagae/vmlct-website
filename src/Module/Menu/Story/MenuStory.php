<?php

declare(strict_types=1);

namespace App\Module\Menu\Story;

use App\Module\Menu\Entity\MenuTargetType;
use App\Module\Menu\Factory\MenuItemFactory;
use App\Page\Entity\Page;
use App\Page\Repository\PageRepository;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'menu')]
final class MenuStory extends Story
{
    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    public function build(): void
    {
        MenuItemFactory::createOne([
            'label' => 'Home',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 0,
        ]);

        MenuItemFactory::createOne([
            'label' => 'Nieuws',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'news_index',
            'position' => 10,
        ]);

        MenuItemFactory::createOne([
            'label' => 'Ploeg',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'team_index',
            'position' => 20,
        ]);

        $over = $this->pageRepository->findOneBy(['path' => 'over']);
        if ($over instanceof Page) {
            MenuItemFactory::createOne([
                'label' => 'Over ons',
                'targetType' => MenuTargetType::Page,
                'page' => $over,
                'position' => 30,
            ]);
        }

        // Sub-menu example: an "Info" dropdown with two child pages.
        $info = MenuItemFactory::createOne([
            'label' => 'Info',
            'targetType' => MenuTargetType::Route,
            'routeName' => 'home',
            'position' => 40,
        ]);

        $uitrusting = $this->pageRepository->findOneBy(['path' => 'uitrusting']);
        if ($uitrusting instanceof Page) {
            MenuItemFactory::createOne([
                'label' => 'Uitrusting',
                'targetType' => MenuTargetType::Page,
                'page' => $uitrusting,
                'parent' => $info,
                'position' => 0,
            ]);
        }
    }
}
