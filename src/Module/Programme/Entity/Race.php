<?php

declare(strict_types=1);

namespace App\Module\Programme\Entity;

use App\Module\Programme\Repository\RaceRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A planned (large) race event published on the public `/programma` page.
 *
 * Regional/local results that don't have a counterpart Race here are
 * tracked in the Uitslagen module via Result with a nullable `race`.
 */
#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[Auditable]
class Race implements HasUlidIdInterface, TimestampableInterface
{
    use TimestampableTrait;
    use UlidIdTrait;

    #[ORM\Column(length: 200)]
    private string $name;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(length: 150)]
    private string $location;

    #[ORM\Column(length: 16, enumType: RaceDiscipline::class)]
    private RaceDiscipline $discipline;

    /**
     * Targeted categories. Persisted as a JSON list of enum string values
     * (e.g. `['junioren', 'nieuwelingen']`); exposed to callers as
     * `RaceCategory` enum cases through `getCategories()`.
     *
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $categories = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $externalUrl = null;

    public function __construct(string $name, \DateTimeImmutable $startsAt, string $location, RaceDiscipline $discipline)
    {
        $this->name = $name;
        $this->startsAt = $startsAt;
        $this->location = $location;
        $this->discipline = $discipline;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): void
    {
        $this->startsAt = $startsAt;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getDiscipline(): RaceDiscipline
    {
        return $this->discipline;
    }

    public function setDiscipline(RaceDiscipline $discipline): void
    {
        $this->discipline = $discipline;
    }

    /**
     * @return list<RaceCategory>
     */
    public function getCategories(): array
    {
        $enums = [];
        foreach ($this->categories as $value) {
            $enum = RaceCategory::tryFrom($value);
            if (null !== $enum) {
                $enums[] = $enum;
            }
        }

        return $enums;
    }

    /**
     * @param iterable<RaceCategory|string> $categories
     */
    public function setCategories(iterable $categories): void
    {
        $values = [];
        foreach ($categories as $cat) {
            $enum = $cat instanceof RaceCategory ? $cat : RaceCategory::tryFrom((string) $cat);
            if (null !== $enum && !\in_array($enum->value, $values, true)) {
                $values[] = $enum->value;
            }
        }
        $this->categories = $values;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getExternalUrl(): ?string
    {
        return $this->externalUrl;
    }

    public function setExternalUrl(?string $externalUrl): void
    {
        $this->externalUrl = $externalUrl;
    }

    public function isUpcoming(): bool
    {
        return $this->startsAt > new \DateTimeImmutable();
    }
}
