<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Enums\TunnelMode;

it('has a quick mode', function (): void {
    expect(TunnelMode::Quick->value)->toBe('quick');
});

it('has a named mode', function (): void {
    expect(TunnelMode::Named->value)->toBe('named');
});

it('can be created from a string value', function (string $value, TunnelMode $expected): void {
    expect(TunnelMode::from($value))->toBe($expected);
})->with([
    ['quick', TunnelMode::Quick],
    ['named', TunnelMode::Named],
]);

it('throws an error for invalid values', function (): void {
    TunnelMode::from('invalid');
})->throws(ValueError::class);
