<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Services;

use Laratusk\CloudflareTunnel\Contracts\CloudflaredResolverInterface;
use Laratusk\CloudflareTunnel\Contracts\ProcessManagerInterface;
use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Enums\TunnelMode;
use Laratusk\CloudflareTunnel\Exceptions\InvalidConfigurationException;
use Laratusk\CloudflareTunnel\Exceptions\TunnelConnectionException;
use Laratusk\CloudflareTunnel\Support\CloudflaredBinary;
use Laratusk\CloudflareTunnel\Support\ProcessManager;

final class TunnelService implements TunnelServiceInterface
{
    private readonly ProcessManagerInterface $process;

    private readonly CloudflaredResolverInterface $binary;

    public function __construct(?ProcessManagerInterface $process = null, ?CloudflaredResolverInterface $binary = null)
    {
        $this->process = $process ?? new ProcessManager;
        $this->binary = $binary ?? new CloudflaredBinary;
    }

    public function start(TunnelConfig $config): string
    {
        $this->validateConfig($config);

        $this->binary->resolve();

        $command = $this->buildCommand($config);

        if (! $this->process->open($command)) {
            throw new TunnelConnectionException('Failed to start the cloudflared process.');
        }

        return $config->mode === TunnelMode::Named
            ? $this->waitForNamedTunnel($config)
            : $this->waitForQuickTunnel($config);
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    public function stop(): void
    {
        $this->process->terminate();
    }

    /**
     * Validate the tunnel configuration before starting.
     */
    private function validateConfig(TunnelConfig $config): void
    {
        if ($config->mode === TunnelMode::Named && ($config->tunnelName === null || $config->hostname === null)) {
            throw InvalidConfigurationException::missingNamedTunnelConfig();
        }
    }

    /**
     * Build the cloudflared command arguments.
     *
     * @return list<string>
     */
    private function buildCommand(TunnelConfig $config): array
    {
        if ($config->mode === TunnelMode::Named) {
            return ['cloudflared', 'tunnel', 'run', (string) $config->tunnelName];
        }

        $command = ['cloudflared', 'tunnel', '--url', $config->localUrl];

        if ($config->hostHeader !== null) {
            $command[] = '--http-host-header';
            $command[] = $config->hostHeader;
        }

        return $command;
    }

    /**
     * Wait for a named tunnel to register its connection.
     */
    private function waitForNamedTunnel(TunnelConfig $config): string
    {
        $deadline = time() + $config->timeout;

        while (time() < $deadline) {
            $line = $this->process->readStderr();

            if ($line !== null && str_contains($line, 'Registered tunnel connection')) {
                return 'https://'.$config->hostname;
            }

            usleep(100_000);
        }

        $this->process->terminate();

        throw new TunnelConnectionException;
    }

    /**
     * Wait for a quick tunnel to output its URL and register.
     */
    private function waitForQuickTunnel(TunnelConfig $config): string
    {
        $deadline = time() + $config->timeout;
        $tunnelUrl = null;
        $registered = false;

        while (time() < $deadline) {
            $line = $this->process->readStderr();

            if ($line !== null) {
                if ($tunnelUrl === null && preg_match('/(https:\/\/[a-z0-9-]+\.trycloudflare\.com)/', $line, $matches) === 1) {
                    $tunnelUrl = $matches[1];
                }

                if (str_contains($line, 'Registered tunnel connection')) {
                    $registered = true;
                }

                if ($tunnelUrl !== null && $registered) {
                    return $tunnelUrl;
                }
            }

            usleep(100_000);
        }

        $this->process->terminate();

        throw new TunnelConnectionException;
    }
}
