<?php

use App\Helpers\HandleHelper;
use App\Models\Shortener;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

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
    put('/api/shortener')->assertStatus(302);

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
