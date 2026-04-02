<?php

declare(strict_types=1);

use Laratusk\CloudflareTunnel\Enums\TunnelMode;

it('has a quick mode', function () {
    expect(TunnelMode::Quick->value)->toBe('quick');
});

it('has a named mode', function () {
    expect(TunnelMode::Named->value)->toBe('named');
});

it('can be created from a string value', function (string $value, TunnelMode $expected) {
    expect(TunnelMode::from($value))->toBe($expected);
})->with([
    ['quick', TunnelMode::Quick],
    ['named', TunnelMode::Named],
]);

it('throws an error for invalid values', function () {
    TunnelMode::from('invalid');
})->throws(ValueError::class);
