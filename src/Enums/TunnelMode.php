<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Enums;

enum TunnelMode: string
{
    case Quick = 'quick';
    case Named = 'named';
}
