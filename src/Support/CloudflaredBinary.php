<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Support;

use Laratusk\CloudflareTunnel\Exceptions\CloudflaredNotFoundException;

final class CloudflaredBinary
{
    /**
     * Resolve the absolute path to the `cloudflared` binary.
     *
     * @throws CloudflaredNotFoundException
     */
    public static function path(): string
    {
        $path = trim((string) shell_exec('which cloudflared'));

        if ($path === '') {
            throw new CloudflaredNotFoundException;
        }

        return $path;
    }
}
