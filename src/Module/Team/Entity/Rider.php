<?php

declare(strict_types=1);

namespace App\Module\Team\Entity;

use App\Module\Team\Repository\RiderRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RiderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Rider implements HasUlidIdInterface, TimestampableInterface
{
    use UlidIdTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 100)]
    private string $firstName;

    #[ORM\Column(length: 100)]
    private string $lastName;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private \DateTimeImmutable $dateOfBirth;

    #[ORM\Column(length: 30, enumType: RiderCategory::class)]
    private RiderCategory $category;

    #[ORM\Column(nullable: true)]
    private ?int $bibNumber = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $photoUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $palmares = null;

    #[ORM\Column]
    private bool $active = true;

    public function __construct(string $firstName, string $lastName, \DateTimeImmutable $dateOfBirth, RiderCategory $category)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dateOfBirth = $dateOfBirth;
        $this->category = $category;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    public function getDateOfBirth(): \DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeImmutable $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getCategory(): RiderCategory
    {
        return $this->category;
    }

    public function setCategory(RiderCategory $category): void
    {
        $this->category = $category;
    }

    public function getBibNumber(): ?int
    {
        return $this->bibNumber;
    }

    public function setBibNumber(?int $bibNumber): void
    {
        $this->bibNumber = $bibNumber;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
    }

    public function getPalmares(): ?string
    {
        return $this->palmares;
    }

    public function setPalmares(?string $palmares): void
    {
        $this->palmares = $palmares;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
