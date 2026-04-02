<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;

final readonly class TunnelConnected
{
    use Dispatchable;

    public function __construct(
        public string $url,
        public TunnelMode $mode,
    ) {}
}
