<?php

declare(strict_types=1);

namespace App\Module\Team\Entity;

enum StaffRole: string
{
    case Director = 'director';
    case Coach = 'coach';
    case Mechanic = 'mechanic';
    case Soigneur = 'soigneur';
    case Manager = 'manager';
    case Volunteer = 'volunteer';

    public function label(): string
    {
        return match ($this) {
            self::Director => 'Bestuur',
            self::Coach => 'Trainer',
            self::Mechanic => 'Mecanicien',
            self::Soigneur => 'Verzorger',
            self::Manager => 'Ploegleider',
            self::Volunteer => 'Vrijwilliger',
        };
    }
}
