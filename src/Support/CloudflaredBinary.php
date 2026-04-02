<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Support;

use Laratusk\CloudflareTunnel\Contracts\CloudflaredResolverInterface;
use Laratusk\CloudflareTunnel\Exceptions\CloudflaredNotFoundException;

final class CloudflaredBinary implements CloudflaredResolverInterface
{
    /**
     * Resolve the absolute path to the `cloudflared` binary.
     *
     * @throws CloudflaredNotFoundException
     */
    public function resolve(): string
    {
        $path = trim((string) shell_exec('which cloudflared'));

        if ($path === '') {
            throw new CloudflaredNotFoundException;
        }

        return $path;
    }
}
