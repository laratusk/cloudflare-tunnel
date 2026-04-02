<?php

declare(strict_types=1);

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
