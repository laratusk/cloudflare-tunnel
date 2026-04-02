<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Contracts\CloudflaredResolverInterface;
use Laratusk\CloudflareTunnel\Contracts\ProcessManagerInterface;
use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;
use Laratusk\CloudflareTunnel\Exceptions\InvalidConfigurationException;
use Laratusk\CloudflareTunnel\Exceptions\TunnelConnectionException;
use Laratusk\CloudflareTunnel\Services\TunnelService;

function mockBinary(): CloudflaredResolverInterface
{
    $binary = Mockery::mock(CloudflaredResolverInterface::class);
    $binary->shouldReceive('resolve')->andReturn('/usr/local/bin/cloudflared');

    return $binary;
}

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

it('starts a named tunnel and returns the static url', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')
        ->with(['cloudflared', 'tunnel', 'run', 'my-tunnel'])
        ->once()
        ->andReturn(true);
    $process->shouldReceive('readStderr')
        ->andReturn(
            "INF Starting tunnel\n",
            "INF Registered tunnel connection connIndex=0\n",
        );

    $service = new TunnelService($process, mockBinary());

    $url = $service->start(new TunnelConfig(
        mode: TunnelMode::Named,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
        tunnelName: 'my-tunnel',
        hostname: 'tunnel.example.com',
    ));

    expect($url)->toBe('https://tunnel.example.com');
});

it('starts a quick tunnel and returns the generated url', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')
        ->with(['cloudflared', 'tunnel', '--url', 'http://127.0.0.1'])
        ->once()
        ->andReturn(true);
    $process->shouldReceive('readStderr')
        ->andReturn(
            "INF |  https://random-words.trycloudflare.com\n",
            "INF Registered tunnel connection connIndex=0\n",
        );

    $service = new TunnelService($process, mockBinary());

    $url = $service->start(new TunnelConfig(
        mode: TunnelMode::Quick,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
    ));

    expect($url)->toBe('https://random-words.trycloudflare.com');
});

it('includes host header in quick tunnel command when configured', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')
        ->with(['cloudflared', 'tunnel', '--url', 'http://127.0.0.1', '--http-host-header', 'myapp.test'])
        ->once()
        ->andReturn(true);
    $process->shouldReceive('readStderr')
        ->andReturn(
            "INF |  https://test-abc.trycloudflare.com\n",
            "INF Registered tunnel connection connIndex=0\n",
        );

    $service = new TunnelService($process, mockBinary());

    $url = $service->start(new TunnelConfig(
        mode: TunnelMode::Quick,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
        hostHeader: 'myapp.test',
    ));

    expect($url)->toBe('https://test-abc.trycloudflare.com');
});

it('throws when process fails to open', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')->once()->andReturn(false);

    $service = new TunnelService($process, mockBinary());

    $service->start(new TunnelConfig(
        mode: TunnelMode::Quick,
        localUrl: 'http://127.0.0.1',
        timeout: 5,
    ));
})->throws(TunnelConnectionException::class, 'Failed to start the cloudflared process.');

it('throws on timeout for named tunnel', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')->once()->andReturn(true);
    $process->shouldReceive('readStderr')->andReturn(null);
    $process->shouldReceive('terminate')->once();

    $service = new TunnelService($process, mockBinary());

    $service->start(new TunnelConfig(
        mode: TunnelMode::Named,
        localUrl: 'http://127.0.0.1',
        timeout: 1,
        tunnelName: 'my-tunnel',
        hostname: 'tunnel.example.com',
    ));
})->throws(TunnelConnectionException::class);

it('throws on timeout for quick tunnel', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('open')->once()->andReturn(true);
    $process->shouldReceive('readStderr')->andReturn(null);
    $process->shouldReceive('terminate')->once();

    $service = new TunnelService($process, mockBinary());

    $service->start(new TunnelConfig(
        mode: TunnelMode::Quick,
        localUrl: 'http://127.0.0.1',
        timeout: 1,
    ));
})->throws(TunnelConnectionException::class);

it('delegates isRunning to process manager', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('isRunning')->once()->andReturn(true);

    $service = new TunnelService($process, mockBinary());

    expect($service->isRunning())->toBeTrue();
});

it('delegates stop to process manager', function (): void {
    $process = Mockery::mock(ProcessManagerInterface::class);
    $process->shouldReceive('terminate')->once();

    $service = new TunnelService($process, mockBinary());

    $service->stop();
});
