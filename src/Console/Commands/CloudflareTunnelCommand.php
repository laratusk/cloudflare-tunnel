<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;
use Laratusk\CloudflareTunnel\Events\TunnelConnected;
use Laratusk\CloudflareTunnel\Events\TunnelDisconnected;
use Laratusk\CloudflareTunnel\Exceptions\CloudflareTunnelException;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;

use Throwable;

final class CloudflareTunnelCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'cloudflare:tunnel';

    /**
     * @var string
     */
    protected $description = 'Start a Cloudflare Tunnel and keep it running in the foreground';

    public function handle(TunnelServiceInterface $tunnel): int
    {
        $config = $this->resolveConfig();

        try {
            /** @var string $url */
            $url = spin(
                callback: fn () => $tunnel->start($config),
                message: 'Starting Cloudflare Tunnel...',
            );
        } catch (CloudflareTunnelException $e) {
            error($e->getMessage());

            return self::FAILURE;
        }

        info("Tunnel URL: {$url}");

        TunnelConnected::dispatch($url, $config->mode);

        if ($config->afterConnected !== null) {
            try {
                spin(
                    callback: fn () => ($config->afterConnected)($url),
                    message: 'Running after-connected callback...',
                );
                info('After-connected callback completed.');
            } catch (Throwable $e) {
                error("After-connected callback failed: {$e->getMessage()}");
            }
        }

        $this->components->info('Tunnel is running. Press Ctrl+C to stop.');

        $this->registerSignalHandlers($tunnel, $config, $url);

        while ($tunnel->isRunning()) {
            sleep(1);
        }

        error('Tunnel process exited unexpectedly.');

        return self::FAILURE;
    }

    private function resolveConfig(): TunnelConfig
    {
        /** @var string $modeValue */
        $modeValue = config('cloudflare-tunnel.mode', 'quick');
        $mode = TunnelMode::from($modeValue);

        /** @var string $localUrl */
        $localUrl = config('cloudflare-tunnel.local_url', 'http://127.0.0.1');

        /** @var int $timeout */
        $timeout = config('cloudflare-tunnel.timeout', 30);

        /** @var string|null $tunnelName */
        $tunnelName = config('cloudflare-tunnel.tunnel_name');

        /** @var string|null $hostname */
        $hostname = config('cloudflare-tunnel.hostname');

        /** @var string|null $hostHeader */
        $hostHeader = config('cloudflare-tunnel.host_header');

        /** @var Closure(string): void|null $afterConnected */
        $afterConnected = config('cloudflare-tunnel.after_connected');

        /** @var Closure(string): void|null $beforeDisconnected */
        $beforeDisconnected = config('cloudflare-tunnel.before_disconnected');

        return new TunnelConfig(
            mode: $mode,
            localUrl: $localUrl,
            timeout: $timeout,
            tunnelName: $tunnelName,
            hostname: $hostname,
            hostHeader: $hostHeader,
            afterConnected: $afterConnected,
            beforeDisconnected: $beforeDisconnected,
        );
    }

    private function registerSignalHandlers(TunnelServiceInterface $tunnel, TunnelConfig $config, string $url): void
    {
        pcntl_async_signals(true);

        $handler = function () use ($tunnel, $config, $url): void {
            $this->newLine();
            info('Stopping tunnel...');

            if ($config->beforeDisconnected !== null) {
                try {
                    ($config->beforeDisconnected)($url);
                    info('Before-disconnected callback completed.');
                } catch (Throwable $e) {
                    error("Before-disconnected callback failed: {$e->getMessage()}");
                }
            }

            $tunnel->stop();

            TunnelDisconnected::dispatch($url);

            info('Tunnel stopped.');

            exit(0);
        };

        pcntl_signal(SIGINT, $handler);
        pcntl_signal(SIGTERM, $handler);
    }
}
