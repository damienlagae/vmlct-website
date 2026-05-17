<?php

declare(strict_types=1);

namespace App\Module\Team\Entity;

enum RiderCategory: string
{
    case Miniemen = 'miniemen';
    case Aspiranten = 'aspiranten';
    case Nieuwelingen = 'nieuwelingen';
    case Junioren = 'junioren';

    public function label(): string
    {
        return match ($this) {
            self::Miniemen => 'Miniemen',
            self::Aspiranten => 'Aspiranten',
            self::Nieuwelingen => 'Nieuwelingen',
            self::Junioren => 'Junioren',
        };
    }

    /**
     * Indicative age range (year of birth based, not strict).
     */
    public function ageRange(): string
    {
        return match ($this) {
            self::Miniemen => '8-11',
            self::Aspiranten => '12-14',
            self::Nieuwelingen => '15-16',
            self::Junioren => '17-18',
        };
    }

    /**
     * @return list<self>
     */
    public static function ordered(): array
    {
        return [self::Miniemen, self::Aspiranten, self::Nieuwelingen, self::Junioren];
    }
}
