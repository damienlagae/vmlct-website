<?php

declare(strict_types=1);

namespace App\Module\Uitslagen\Entity;

/**
 * What happened to the rider in the race. `Finished` is the only status
 * that carries a meaningful `rank`; DNF/DNS/DSQ keep rank null.
 */
enum ResultStatus: string
{
    case Finished = 'finished';
    case Dnf = 'dnf';
    case Dns = 'dns';
    case Dsq = 'dsq';
}
