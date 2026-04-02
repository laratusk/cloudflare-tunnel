<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Exceptions;

final class InvalidConfigurationException extends CloudflareTunnelException
{
    public static function missingNamedTunnelConfig(): self
    {
        return new self(
            'Named tunnel mode requires both `tunnel_name` and `hostname` to be configured. '
            .'Set CLOUDFLARE_TUNNEL_NAME and CLOUDFLARE_TUNNEL_HOSTNAME in your .env file.',
        );
    }
}
