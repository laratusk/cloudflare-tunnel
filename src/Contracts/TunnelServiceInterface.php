<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Contracts;

use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;

interface TunnelServiceInterface
{
    /**
     * Start the tunnel process and return the public URL.
     */
    public function start(TunnelConfig $config): string;

    /**
     * Check if the tunnel process is still running.
     */
    public function isRunning(): bool;

    /**
     * Stop the tunnel process and clean up resources.
     */
    public function stop(): void;
}
