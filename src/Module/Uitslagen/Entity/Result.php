<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Entity;

use App\Module\Programme\Entity\Race;
use App\Module\Team\Entity\Rider;
use App\Module\Uitslagen\Repository\ResultRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use DH\Auditor\Attribute\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A single rider's result at a race. The race must exist in the Race
 * table — regional events not in the public Programme are still created
 * as Race rows (admins simply pick whether to surface them publicly).
 *
 * For multi-stage races, `stageNumber` identifies which stage the rank
 * refers to. Leave it null for single-day events.
 */
#[ORM\Entity(repositoryClass: ResultRepository::class)]
#[Auditable]
class Result implements HasUlidIdInterface, TimestampableInterface
{
    use TimestampableTrait;
    use UlidIdTrait;

    #[ORM\ManyToOne(targetEntity: Rider::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Rider $rider;

    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Race $race;

    /**
     * Stage number for multi-stage races (1-indexed). Null for single-day
     * events.
     */
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $stageNumber = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $rank;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function __construct(Rider $rider, Race $race, int $rank)
    {
        $this->rider = $rider;
        $this->race = $race;
        $this->rank = $rank;
    }

    public function getRider(): Rider
    {
        return $this->rider;
    }

    public function setRider(Rider $rider): void
    {
        $this->rider = $rider;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
    }

    public function getStageNumber(): ?int
    {
        return $this->stageNumber;
    }

    public function setStageNumber(?int $stageNumber): void
    {
        $this->stageNumber = $stageNumber;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setRank(int $rank): void
    {
        $this->rank = $rank;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}
