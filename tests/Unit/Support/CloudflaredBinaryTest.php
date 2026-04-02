<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Exceptions\CloudflaredNotFoundException;
use Laratusk\CloudflareTunnel\Support\CloudflaredBinary;

it('returns a non-empty path when cloudflared is installed', function () {
    $path = CloudflaredBinary::path();

    expect($path)->toBeString()
        ->and($path)->not->toBeEmpty()
        ->and($path)->toContain('cloudflared');
});

it('throws an exception when cloudflared is not found', function () {
    // Save the current PATH
    $originalPath = getenv('PATH');

    // Set PATH to empty so cloudflared cannot be found
    putenv('PATH=');

    try {
        CloudflaredBinary::path();
    } finally {
        // Restore the original PATH
        putenv("PATH={$originalPath}");
    }
})->throws(CloudflaredNotFoundException::class);
