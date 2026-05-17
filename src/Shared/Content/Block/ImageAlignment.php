<?php

declare(strict_types=1);

namespace App\Shared\Content\Block;

enum ImageAlignment: string
{
    case Left = 'left';
    case Right = 'right';
    case Center = 'center';
    case Full = 'full';
}
