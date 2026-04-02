<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;

it('can be constructed with minimal parameters', function () {
    $config = new TunnelConfig(
        mode: TunnelMode::Quick,
        localUrl: 'http://127.0.0.1',
        timeout: 30,
    );

    expect($config->mode)->toBe(TunnelMode::Quick)
        ->and($config->localUrl)->toBe('http://127.0.0.1')
        ->and($config->timeout)->toBe(30)
        ->and($config->tunnelName)->toBeNull()
        ->and($config->hostname)->toBeNull()
        ->and($config->hostHeader)->toBeNull()
        ->and($config->afterConnected)->toBeNull()
        ->and($config->beforeDisconnected)->toBeNull();
});

it('can be constructed with all parameters', function () {
    $afterConnected = fn (string $url) => null;
    $beforeDisconnected = fn (string $url) => null;

    $config = new TunnelConfig(
        mode: TunnelMode::Named,
        localUrl: 'http://127.0.0.1:8080',
        timeout: 60,
        tunnelName: 'my-tunnel',
        hostname: 'tunnel.example.com',
        hostHeader: 'myapp.test',
        afterConnected: $afterConnected,
        beforeDisconnected: $beforeDisconnected,
    );

    expect($config->mode)->toBe(TunnelMode::Named)
        ->and($config->localUrl)->toBe('http://127.0.0.1:8080')
        ->and($config->timeout)->toBe(60)
        ->and($config->tunnelName)->toBe('my-tunnel')
        ->and($config->hostname)->toBe('tunnel.example.com')
        ->and($config->hostHeader)->toBe('myapp.test')
        ->and($config->afterConnected)->toBe($afterConnected)
        ->and($config->beforeDisconnected)->toBe($beforeDisconnected);
});
