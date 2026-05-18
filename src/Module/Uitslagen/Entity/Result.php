<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Entity;

use App\Module\Programme\Entity\Race;
use App\Module\Programme\Entity\RaceStage;
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
 * A single rider's outcome on a specific RaceStage. Every result is
 * attached to a stage (an implicit single-stage exists for one-day
 * races); the race is reached through `$stage->getRace()`.
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

    #[ORM\ManyToOne(targetEntity: RaceStage::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private RaceStage $stage;

    #[ORM\Column(type: Types::INTEGER)]
    private int $rank;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function __construct(Rider $rider, RaceStage $stage, int $rank)
    {
        $this->rider = $rider;
        $this->stage = $stage;
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

    public function getStage(): RaceStage
    {
        return $this->stage;
    }

    public function setStage(RaceStage $stage): void
    {
        $this->stage = $stage;
    }

    public function getRace(): Race
    {
        return $this->stage->getRace();
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
