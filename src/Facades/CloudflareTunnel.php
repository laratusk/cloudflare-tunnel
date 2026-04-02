<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Facades;

use Illuminate\Support\Facades\Facade;
use Laratusk\CloudflareTunnel\Contracts\TunnelServiceInterface;
use Laratusk\CloudflareTunnel\DTOs\TunnelConfig;
use Laratusk\CloudflareTunnel\Services\TunnelService;

/**
 * @method static string start(TunnelConfig $config)
 * @method static bool isRunning()
 * @method static void stop()
 *
 * @see TunnelService
 */
final class CloudflareTunnel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TunnelServiceInterface::class;
    }
}
