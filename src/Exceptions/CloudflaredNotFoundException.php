<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Exceptions;

final class CloudflaredNotFoundException extends CloudflareTunnelException
{
    public function __construct()
    {
        parent::__construct(
            'The `cloudflared` binary was not found. Install it with: brew install cloudflared (macOS) or see https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/',
        );
    }
}
