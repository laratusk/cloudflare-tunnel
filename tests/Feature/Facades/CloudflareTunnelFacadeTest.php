<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\Facades\CloudflareTunnel;
use Laratusk\CloudflareTunnel\Services\TunnelService;

it('resolves the tunnel service from the container', function (): void {
    $service = app(TunnelServiceInterface::class);

    expect($service)->toBeInstanceOf(TunnelService::class);
});

it('resolves as a singleton', function (): void {
    $first = app(TunnelServiceInterface::class);
    $second = app(TunnelServiceInterface::class);

    expect($first)->toBe($second);
});

it('provides a facade accessor', function (): void {
    expect(CloudflareTunnel::isRunning())->toBeFalse();
});
