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

// Test for referrer tracking
it('appends referrer to redirect URL when present', function () {
    $shortener = Shortener::factory()->create([
        'original_url' => 'https://example.com/page',
    ]);

    $referer = 'https://google.com';

    $response = get($shortener->getRedirectUrl(), ['Referer' => $referer]);

    $response->assertStatus(302);

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('referer=')
        ->toContain('https%3A%2F%2Fgoogle.com');
});

// Test for UTM parameter tracking
it('appends UTM parameters to redirect URL when present', function () {
    $shortener = Shortener::factory()->create([
        'original_url' => 'https://example.com/page',
    ]);

    $response = get($shortener->getRedirectUrl() . '?utm_source=twitter&utm_medium=social&utm_campaign=promo');

    $response->assertStatus(302);

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('utm_source=twitter')
        ->toContain('utm_medium=social')
        ->toContain('utm_campaign=promo');
});

// Test for combined referrer + UTM parameters
it('appends both referrer and UTM parameters to redirect URL', function () {
    $shortener = Shortener::factory()->create([
        'original_url' => 'https://example.com/page',
    ]);

    $referer = 'https://facebook.com';

    $response = get($shortener->getRedirectUrl() . '?utm_source=google&utm_campaign=spring_sale', ['Referer' => $referer]);

    $response->assertStatus(302);

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('referer=')
        ->toContain('utm_source=google')
        ->toContain('utm_campaign=spring_sale');
});

// Test that existing query params are preserved
it('preserves existing query parameters in original URL when appending tracking params', function () {
    $shortener = Shortener::factory()->create([
        'original_url' => 'https://example.com/page?existing=param',
    ]);

    $response = get($shortener->getRedirectUrl(), ['Referer' => 'https://google.com']);

    $response->assertStatus(302);

    $location = $response->headers->get('Location');

    expect($location)
        ->toContain('existing=param')
        ->toContain('referer=');
});
