<?php

declare(strict_types=1);

namespace App\Module\Programme\Entity;

/**
 * Duplicated from Team\RiderCategory by design: each module owns its enums
 * so it stays extractable by copy/paste. Same values, no cross-module
 * coupling.
 */
enum RaceCategory: string
{
    case Miniemen = 'miniemen';
    case Aspiranten = 'aspiranten';
    case Nieuwelingen = 'nieuwelingen';
    case Junioren = 'junioren';
}
