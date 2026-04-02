<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Events;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class TunnelDisconnected
{
    use Dispatchable;

    public function __construct(
        public string $url,
    ) {}
}
