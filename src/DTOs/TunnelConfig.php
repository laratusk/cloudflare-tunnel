<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\DTOs;

use Closure;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;

final readonly class TunnelConfig
{
    /**
     * @param  Closure(string): void|null  $afterConnected
     * @param  Closure(string): void|null  $beforeDisconnected
     */
    public function __construct(
        public TunnelMode $mode,
        public string $localUrl,
        public int $timeout,
        public ?string $tunnelName = null,
        public ?string $hostname = null,
        public ?string $hostHeader = null,
        public ?Closure $afterConnected = null,
        public ?Closure $beforeDisconnected = null,
    ) {}
}
