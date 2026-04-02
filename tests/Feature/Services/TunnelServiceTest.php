<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;
use Laratusk\CloudflareTunnel\Exceptions\InvalidConfigurationException;
use Laratusk\CloudflareTunnel\Services\TunnelService;

it('throws when named tunnel config is missing tunnel name', function (): void {
    $service = new TunnelService;

    $config = new TunnelConfig(
        mode: TunnelMode::Named,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
    );

    $service->start($config);
})->throws(InvalidConfigurationException::class);

it('throws when named tunnel config is missing hostname', function (): void {
    $service = new TunnelService;

    $config = new TunnelConfig(
        mode: TunnelMode::Named,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
        tunnelName: 'my-tunnel',
    );

    $service->start($config);
})->throws(InvalidConfigurationException::class);

it('reports not running when not started', function (): void {
    $service = new TunnelService;

    expect($service->isRunning())->toBeFalse();
});
