<?php

use App\Helpers\HandleHelper;
use App\Models\Shortener;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\putJson;

beforeEach(function () {
    actingAs(User::orderBy('id')->first());
});

it('can create by factory', function () {
    Shortener::factory()->count(3)->create();
})->throwsNoExceptions();

it('can request URL', function () {
    $shortener = Shortener::factory()->create();

    expect($shortener->getRedirectUrl())
        ->toBeUrl()
        ->toStartWith(env('APP_URL'))
        ->toEndWith($shortener->handle);
});

it('can create', function () {
    putJson('/api/shortener')->assertStatus(422);

    $shortenerArr = putJson('/api/shortener', [
        'original_url' => 'invalid-url',
    ])
        ->assertStatus(422)
        ->json();

    $shortenerArr = put('/api/shortener', [
        'original_url' => $url = fake()->url(),
    ])
        ->assertStatus(201)
        ->json();

    expect($shortenerArr)->toHaveKeys([
        'id',
        'original_url',
        'handle',
    ]);

    expect($shortenerArr['original_url'])->toBe($url);
});

it('cannot get non-existing', function () {
    get('/api/shortener/999999')->assertStatus(404);

    get(HandleHelper::getNewHandle())->assertStatus(404);
});

it('can get', function () {
    $shortener = Shortener::factory()->create();

    $shortenerArr = get("/api/shortener/{$shortener->id}")->assertStatus(200)->json();

    expect($shortenerArr)->toMatchArray($shortener->toArray());
});

it('cannot get if wrong user', function () {
    $shortener = Shortener::factory()->create();

    actingAs(User::factory()->create());

    get("/api/shortener/{$shortener->id}")->assertStatus(401);
});

it('cannot get unexisting', function () {
    get('/api/shortener/999999')->assertStatus(404);

    get(HandleHelper::getNewHandle())->assertStatus(404);
});

it('can delete', function () {
    $shortener = Shortener::factory()->create();

    delete("/api/shortener/{$shortener->id}")->assertStatus(200);
    delete("/api/shortener/{$shortener->id}")->assertStatus(404);

    expect(Shortener::find($shortener->id))->toBeNull();
});

it('cannot delete unexisting', function () {
    delete('/api/shortener/999999')->assertStatus(404);
});

it('can search', function () {
    Shortener::factory()->count(5)->create();

    $result = post('/api/shortener/search')->assertStatus(200)->json();

    expect($result)->toHaveKeys(['data', 'per_page', 'total']);
    expect($result['total'])->toBeGreaterThan(4);
    expect($result['data'][0])->toHaveKey('redirect_url');

    $shortener = Shortener::factory()->create();

    /**
     * Search by original_url.
     */
    $result = post('/api/shortener/search', [
        'search' => $shortener->original_url,
    ])->assertStatus(200)->json();

    expect($result['data'])->toHaveCount(1);
    expect($result['data'][0]['id'])->toBe($shortener->id);

    /**
     * Search by handle.
     */
    $result = post('/api/shortener/search', [
        'search' => $shortener->handle,
    ])->assertStatus(200)->json();

    expect($result['data'])->toHaveCount(1);
    expect($result['data'][0]['handle'])->toBe($shortener->handle);
});
