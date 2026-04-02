<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Contracts;

interface CloudflaredResolverInterface
{
    /**
     * Resolve the absolute path to the `cloudflared` binary.
     */
    public function resolve(): string;
}
