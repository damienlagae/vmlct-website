<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Entity;

/**
 * Duplicated from Programme by design (cross-module isolation). Same
 * values; each module owns its own enum so it stays extractable.
 */
enum ResultDiscipline: string
{
    case Road = 'road';
    case Track = 'track';
    case Cyclocross = 'cyclocross';
}
