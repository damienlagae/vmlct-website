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
 * Single rider result. Optionally tied to a planned `Race` in the
 * Programme module; when not (regional or one-off events) the race
 * info travels on the row itself (`raceName`, `raceDate`, ...).
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

    /**
     * Optional link to a planned Race. If null, the race details below
     * are authoritative (regional / external race not in the agenda).
     */
    #[ORM\ManyToOne(targetEntity: Race::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Race $race = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $raceName = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $raceDate = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $raceLocation = null;

    #[ORM\Column(length: 16, enumType: ResultDiscipline::class, nullable: true)]
    private ?ResultDiscipline $discipline = null;

    #[ORM\Column(length: 16, enumType: ResultStatus::class)]
    private ResultStatus $status = ResultStatus::Finished;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $rank = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function __construct(Rider $rider)
    {
        $this->rider = $rider;
    }

    public function getRider(): Rider
    {
        return $this->rider;
    }

    public function setRider(Rider $rider): void
    {
        $this->rider = $rider;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): void
    {
        $this->race = $race;
    }

    public function getRaceName(): ?string
    {
        return $this->raceName;
    }

    public function setRaceName(?string $raceName): void
    {
        $this->raceName = $raceName;
    }

    public function getRaceDate(): ?\DateTimeImmutable
    {
        return $this->raceDate;
    }

    public function setRaceDate(?\DateTimeImmutable $raceDate): void
    {
        $this->raceDate = $raceDate;
    }

    public function getRaceLocation(): ?string
    {
        return $this->raceLocation;
    }

    public function setRaceLocation(?string $raceLocation): void
    {
        $this->raceLocation = $raceLocation;
    }

    public function getDiscipline(): ?ResultDiscipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?ResultDiscipline $discipline): void
    {
        $this->discipline = $discipline;
    }

    public function getStatus(): ResultStatus
    {
        return $this->status;
    }

    public function setStatus(ResultStatus $status): void
    {
        $this->status = $status;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): void
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

    /**
     * Race label / date / location resolved from the link first, then
     * the on-row fallback fields. Returns nullable strings so the public
     * template can render `—` when truly missing.
     */
    public function getEffectiveRaceName(): ?string
    {
        return $this->race?->getName() ?? $this->raceName;
    }

    public function getEffectiveRaceDate(): ?\DateTimeImmutable
    {
        return $this->race?->getStartsAt() ?? $this->raceDate;
    }

    public function getEffectiveRaceLocation(): ?string
    {
        return $this->race?->getLocation() ?? $this->raceLocation;
    }

    public function getEffectiveDiscipline(): ?ResultDiscipline
    {
        if (null !== $this->race) {
            return ResultDiscipline::tryFrom($this->race->getDiscipline()->value);
        }

        return $this->discipline;
    }
}
