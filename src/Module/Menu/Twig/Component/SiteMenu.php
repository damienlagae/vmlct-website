<?php

declare(strict_types=1);

namespace App\Module\Menu\Twig\Component;

use App\Module\Menu\Entity\MenuItem;
use App\Module\Menu\Entity\MenuTargetType;
use App\Module\Menu\Repository\MenuItemRepository;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Renders the dynamic navbar (root items + one level of dropdown children).
 * Resolves each item's URL on the fly so the menu stays in sync with the
 * Page path (renaming a page updates the link without touching the menu).
 *
 * Usage: <twig:Site:Menu />
 */
#[AsTwigComponent(name: 'Site:Menu', template: '@Menu/components/SiteMenu.html.twig')]
final class SiteMenu
{
    public function __construct(
        private readonly MenuItemRepository $repository,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @return list<MenuItem>
     */
    public function getRoots(): array
    {
        return $this->repository->findActiveTree();
    }

    /**
     * Resolves a menu item to its public URL. Returns `#` when the target is
     * dangling (a removed page, an unknown route) so the navbar never crashes
     * on bad data — admin still sees the broken entry and can fix it.
     */
    public function url(MenuItem $item): string
    {
        try {
            return match ($item->getTargetType()) {
                MenuTargetType::Page => null !== $item->getPage()
                    ? $this->router->generate('page_show', ['path' => $item->getPage()->getPath()], UrlGeneratorInterface::ABSOLUTE_PATH)
                    : '#',
                MenuTargetType::Route => null !== $item->getRouteName()
                    ? $this->router->generate($item->getRouteName(), [], UrlGeneratorInterface::ABSOLUTE_PATH)
                    : '#',
            };
        } catch (RouteNotFoundException) {
            return '#';
        }
    }
}
