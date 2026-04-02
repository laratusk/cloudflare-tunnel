<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel;

use Illuminate\Support\ServiceProvider;
use Laratusk\CloudflareTunnel\Console\Commands\CloudflareTunnelCommand;
use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\Services\TunnelService;

final class CloudflareTunnelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cloudflare-tunnel.php', 'cloudflare-tunnel');

        $this->app->singleton(TunnelServiceInterface::class, TunnelService::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cloudflare-tunnel.php' => config_path('cloudflare-tunnel.php'),
            ], 'cloudflare-tunnel-config');

            $this->commands([
                CloudflareTunnelCommand::class,
            ]);
        }
    }
}
