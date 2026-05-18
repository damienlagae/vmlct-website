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
 * A race in the team's racing calendar. Dates are tracked at day
 * precision: `startDate` for single-day events, plus an optional
 * `endDate` to model multi-stage races (Tour-like).
 */
#[ORM\Entity(repositoryClass: RaceRepository::class)]
#[Auditable]
class Race implements HasUlidIdInterface, TimestampableInterface
{
    use TimestampableTrait;
    use UlidIdTrait;

    #[ORM\Column(length: 200)]
    private string $name;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $startDate;

    /**
     * Last stage date for multi-day races. `null` for single-day events.
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(length: 150)]
    private string $location;

    #[ORM\Column(length: 16, enumType: RaceDiscipline::class)]
    private RaceDiscipline $discipline;

    /**
     * Targeted categories. Persisted as a JSON list of enum string values.
     *
     * @var list<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $categories = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $externalUrl = null;

    public function __construct(string $name, \DateTimeImmutable $startDate, string $location, RaceDiscipline $discipline)
    {
        $this->name = $name;
        $this->startDate = $startDate;
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

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function isMultiStage(): bool
    {
        return null !== $this->endDate && $this->endDate->format('Y-m-d') !== $this->startDate->format('Y-m-d');
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
        $todayStart = new \DateTimeImmutable('today');
        $lastDay = $this->endDate ?? $this->startDate;

        return $lastDay >= $todayStart;
    }
}
