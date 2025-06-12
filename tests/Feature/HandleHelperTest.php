<?php

use App\Helpers\HandleHelper;

it('can generate handle', function () {
    expect(HandleHelper::getNewHandle())->not()->toBeEmpty();
});

it('can generate unique handles', function () {
    expect(collect([
        HandleHelper::getNewHandle(),
        HandleHelper::getNewHandle(),
        HandleHelper::getNewHandle(),
    ])->unique())->toHaveCount(3);
});

it('can get correct alphabet', function () {
    expect(HandleHelper::toAlphabet('ABCDEFG'))->toHaveLength(64);
    expect(HandleHelper::toAlphabet('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-'))->toHaveLength(64);
    expect(HandleHelper::toAlphabet('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-MORE'))->toHaveLength(64);
});

it('can calculate combinations', function () {
    expect(HandleHelper::getCombinationCount())->toBe(68719476736);

    expect(HandleHelper::getCombinationCount('ABC', 3))->toBe(27);
    expect(HandleHelper::getCombinationCount('ABC', 6))->toBe(729);
    expect(HandleHelper::getCombinationCount('ABCEFG', 3))->toBe(216);
    expect(HandleHelper::getCombinationCount('ABCEFG', 6))->toBe(46656);
});
