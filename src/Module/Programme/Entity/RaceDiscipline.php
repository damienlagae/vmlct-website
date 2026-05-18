<?php

declare(strict_types=1);

namespace App\Module\Programme\Entity;

enum RaceDiscipline: string
{
    case Road = 'road';
    case Track = 'track';
    case Cyclocross = 'cyclocross';
}
