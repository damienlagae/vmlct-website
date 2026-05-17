<?php

declare(strict_types=1);

namespace App\Module\Team\Entity;

use App\Module\Team\Repository\StaffRepository;
use App\Shared\Entity\HasUlidIdInterface;
use App\Shared\Entity\TimestampableInterface;
use App\Shared\Entity\TimestampableTrait;
use App\Shared\Entity\UlidIdTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StaffRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Staff implements HasUlidIdInterface, TimestampableInterface
{
    use UlidIdTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 100)]
    private string $firstName;

    #[ORM\Column(length: 100)]
    private string $lastName;

    #[ORM\Column(length: 30, enumType: StaffRole::class)]
    private StaffRole $role;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    #[ORM\Column]
    private bool $active = true;

    public function __construct(string $firstName, string $lastName, StaffRole $role)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
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

    public function getRole(): StaffRole
    {
        return $this->role;
    }

    public function setRole(StaffRole $role): void
    {
        $this->role = $role;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): void
    {
        $this->photoPath = $photoPath;
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
