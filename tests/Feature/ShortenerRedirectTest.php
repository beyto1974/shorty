<?php

use App\Helpers\HandleHelper;
use App\Models\Shortener;

use function Pest\Laravel\get;

it('cannot redirect non-existing', function () {
    get(HandleHelper::getNewHandle())->assertStatus(404);
});

it('can redirect', function () {
    $shortener = Shortener::factory()->create();

    expect($shortener->hits)->toBe(null);

    get($shortener->getRedirectUrl())
        ->assertStatus(302)
        ->assertHeader('Location', $shortener->original_url);

    expect($shortener->refresh()->hits)->toBe(1);
});
