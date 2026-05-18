<?php

declare(strict_types=1);

namespace App\Module\Programme\Entity;

use App\Module\Programme\Repository\RaceStageRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A stage of a Race. For single-day races, a single RaceStage is created
 * implicitly when the first result lands — its `name` and `stageDate`
 * stay null and the public side falls back to `race.startDate` /
 * `race.name`. Multi-day races may have several stages on the same day
 * (e.g. a morning prologue + an afternoon stage).
 */
#[ORM\Entity(repositoryClass: RaceStageRepository::class)]
#[Auditable]
class RaceStage implements HasUlidIdInterface, TimestampableInterface
{
    use TimestampableTrait;
    use UlidIdTrait;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Race $race;

    /**
     * Optional label like "Prologue" or "Etappe 2 — Dijon → Paris".
     * Null for single-day races.
     */
    #[ORM\Column(length: 200, nullable: true)]
    private ?string $name = null;

    /**
     * Optional explicit date — useful when several stages share a day
     * or differ from `race.startDate`. Fallback resolution lives on
     * `getEffectiveDate()`.
     */
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $stageDate = null;

    /**
     * Sequence within the race (1-indexed). Two stages with the same
     * position is invalid at the data level; the admin enforces it.
     */
    #[ORM\Column(type: Types::SMALLINT)]
    private int $position = 1;

    public function __construct(Race $race, int $position = 1)
    {
        $this->race = $race;
        $this->position = $position;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStageDate(): ?\DateTimeImmutable
    {
        return $this->stageDate;
    }

    public function setStageDate(?\DateTimeImmutable $stageDate): void
    {
        $this->stageDate = $stageDate;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getEffectiveDate(): \DateTimeImmutable
    {
        return $this->stageDate ?? $this->race->getStartDate();
    }

    public function getEffectiveName(): string
    {
        return $this->name ?? $this->race->getName();
    }
}
