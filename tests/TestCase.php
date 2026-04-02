<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Tests;

use Laratusk\CloudflareTunnel\CloudflareTunnelServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CloudflareTunnelServiceProvider::class,
        ];
    }
}
