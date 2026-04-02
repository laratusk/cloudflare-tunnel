<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Support\ProcessManager;

it('can open and terminate a process', function (): void {
    $manager = new ProcessManager;

    $result = $manager->open(['echo', 'hello']);

    expect($result)->toBeTrue();

    $manager->terminate();

    expect($manager->isRunning())->toBeFalse();
});

it('reports not running when no process is started', function (): void {
    $manager = new ProcessManager;

    expect($manager->isRunning())->toBeFalse();
});

it('returns null from stderr when no process is started', function (): void {
    $manager = new ProcessManager;

    expect($manager->readStderr())->toBeNull();
});

it('can read stderr output', function (): void {
    $manager = new ProcessManager;

    // Write to stderr using bash
    $manager->open(['bash', '-c', 'echo "test output" >&2']);

    // Give it a moment to write
    usleep(200_000);

    $output = $manager->readStderr();

    expect($output)->toContain('test output');

    $manager->terminate();
});
