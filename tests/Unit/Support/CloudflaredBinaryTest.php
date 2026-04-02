<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Exceptions\CloudflaredNotFoundException;
use Laratusk\CloudflareTunnel\Support\CloudflaredBinary;

it('returns a non-empty path when cloudflared is installed', function (): void {
    $path = CloudflaredBinary::path();

    expect($path)->toBeString()
        ->and($path)->not->toBeEmpty()
        ->and($path)->toContain('cloudflared');
})->skip((bool) getenv('CI'), 'Requires cloudflared binary installed locally.');

it('throws an exception when cloudflared is not found', function (): void {
    $originalPath = getenv('PATH');

    putenv('PATH=');

    try {
        CloudflaredBinary::path();
    } finally {
        putenv('PATH='.$originalPath);
    }
})->throws(CloudflaredNotFoundException::class);
