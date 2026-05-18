<?php

declare(strict_types=1);

namespace App\Module\Menu\Entity;

use App\Module\Menu\Repository\MenuItemRepository;
use App\Page\Entity\Page;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Navigation entry. Single global menu; hierarchy is one level deep
 * (root items + optional children = Bootstrap dropdowns).
 *
 * Targets are either a Page (ManyToOne, follows the page's `path`) or a
 * Symfony route name picked from a whitelist exposed by the form.
 */
#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
#[Auditable]
class MenuItem implements HasUlidIdInterface, TimestampableInterface
{
    use UlidIdTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 100)]
    private string $label;

    #[ORM\Column(length: 16, enumType: MenuTargetType::class)]
    private MenuTargetType $targetType;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Page $page = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $routeName = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['remove'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $children;

    #[ORM\Column(type: Types::INTEGER)]
    private int $position = 0;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column]
    private bool $openInNewTab = false;

    public function __construct(string $label, MenuTargetType $targetType)
    {
        $this->label = $label;
        $this->targetType = $targetType;
        $this->children = new ArrayCollection();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getTargetType(): MenuTargetType
    {
        return $this->targetType;
    }

    public function setTargetType(MenuTargetType $targetType): void
    {
        $this->targetType = $targetType;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): void
    {
        $this->page = $page;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(?string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isOpenInNewTab(): bool
    {
        return $this->openInNewTab;
    }

    public function setOpenInNewTab(bool $openInNewTab): void
    {
        $this->openInNewTab = $openInNewTab;
    }

    public function isRoot(): bool
    {
        return null === $this->parent;
    }
}
