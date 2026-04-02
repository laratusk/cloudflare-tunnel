<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\Events\TunnelConnected;
use Laratusk\CloudflareTunnel\Exceptions\CloudflaredNotFoundException;

it('is registered as an artisan command', function (): void {
    $this->artisan('list')
        ->expectsOutputToContain('cloudflare:tunnel');
});

it('publishes the config file', function (): void {
    $this->artisan('vendor:publish', [
        '--tag' => 'cloudflare-tunnel-config',
        '--force' => true,
    ])->assertSuccessful();

    $configPath = config_path('cloudflare-tunnel.php');

    expect(file_exists($configPath))->toBeTrue();

    // Clean up
    unlink($configPath);
});

it('returns failure when tunnel service throws', function (): void {
    $mock = Mockery::mock(TunnelServiceInterface::class);
    $mock->shouldReceive('start')->once()->andThrow(new CloudflaredNotFoundException);

    $this->app->instance(TunnelServiceInterface::class, $mock);

    $this->artisan('cloudflare:tunnel')
        ->assertFailed();
});

it('dispatches TunnelConnected event on success', function (): void {
    Event::fake([TunnelConnected::class]);

    $mock = Mockery::mock(TunnelServiceInterface::class);
    $mock->shouldReceive('start')->once()->andReturn('https://tunnel.example.com');
    $mock->shouldReceive('isRunning')->andReturn(false);

    $this->app->instance(TunnelServiceInterface::class, $mock);

    $this->artisan('cloudflare:tunnel');

    Event::assertDispatched(TunnelConnected::class, function (TunnelConnected $event): bool {
        return $event->url === 'https://tunnel.example.com';
    });
});

it('runs after_connected callback when configured', function (): void {
    $callbackRan = false;

    config()->set('cloudflare-tunnel.after_connected', function (string $url) use (&$callbackRan): void {
        $callbackRan = true;
        expect($url)->toBe('https://tunnel.example.com');
    });

    $mock = Mockery::mock(TunnelServiceInterface::class);
    $mock->shouldReceive('start')->once()->andReturn('https://tunnel.example.com');
    $mock->shouldReceive('isRunning')->andReturn(false);

    $this->app->instance(TunnelServiceInterface::class, $mock);

    $this->artisan('cloudflare:tunnel');

    expect($callbackRan)->toBeTrue();
});

it('handles after_connected callback failure gracefully', function (): void {
    config()->set('cloudflare-tunnel.after_connected', function (): void {
        throw new RuntimeException('Webhook failed');
    });

    $mock = Mockery::mock(TunnelServiceInterface::class);
    $mock->shouldReceive('start')->once()->andReturn('https://tunnel.example.com');
    $mock->shouldReceive('isRunning')->andReturn(false);

    $this->app->instance(TunnelServiceInterface::class, $mock);

    // Should not throw, should handle gracefully
    $this->artisan('cloudflare:tunnel')
        ->assertFailed(); // Fails because tunnel exits (isRunning = false)
});

it('reads config values correctly', function (): void {
    config()->set('cloudflare-tunnel.mode', 'named');
    config()->set('cloudflare-tunnel.tunnel_name', 'test-tunnel');
    config()->set('cloudflare-tunnel.hostname', 'test.example.com');
    config()->set('cloudflare-tunnel.local_url', 'http://localhost:8080');
    config()->set('cloudflare-tunnel.host_header', 'myapp.test');
    config()->set('cloudflare-tunnel.timeout', 60);

    $mock = Mockery::mock(TunnelServiceInterface::class);
    $mock->shouldReceive('start')
        ->once()
        ->withArgs(function ($config): bool {
            return $config->mode->value === 'named'
                && $config->tunnelName === 'test-tunnel'
                && $config->hostname === 'test.example.com'
                && $config->localUrl === 'http://localhost:8080'
                && $config->hostHeader === 'myapp.test'
                && $config->timeout === 60;
        })
        ->andReturn('https://test.example.com');
    $mock->shouldReceive('isRunning')->andReturn(false);

    $this->app->instance(TunnelServiceInterface::class, $mock);

    $this->artisan('cloudflare:tunnel');
});
