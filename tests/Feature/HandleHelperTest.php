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
