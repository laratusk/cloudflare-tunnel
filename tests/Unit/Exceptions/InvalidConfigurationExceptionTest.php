<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Exceptions\InvalidConfigurationException;

it('creates a missing named tunnel config exception', function () {
    $exception = InvalidConfigurationException::missingNamedTunnelConfig();

    expect($exception)
        ->toBeInstanceOf(InvalidConfigurationException::class)
        ->and($exception->getMessage())->toContain('CLOUDFLARE_TUNNEL_NAME')
        ->and($exception->getMessage())->toContain('CLOUDFLARE_TUNNEL_HOSTNAME');
});
