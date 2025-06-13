<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\withToken;

beforeEach(function () {
    actingAs(User::orderBy('id')->first());
});

it('can get user stats', function () {

    $stats = getJson('/api/user/stats')->assertStatus(200)->json();
    expect($stats)->toHaveKeys([
        'used',
        'free',
        'total',
        'hits',
    ]);
});

it('can get global stats', function () {
    $stats = getJson('/api/stats')->assertStatus(401)->json();
    $stats = withToken('invalid-token')->getJson('/api/stats')->assertStatus(401)->json();
    $stats = withToken(Config::string('app.master_token'))->getJson('/api/stats')->assertStatus(200)->json();

    expect($stats)->toHaveKeys([
        'global',
        'users',
    ]);

    expect($stats['global'])->toHaveKeys([
        'used',
        'free',
        'total',
        'hits',
    ]);

    expect($stats['users'][0])->toHaveKeys([
        'user',
        'stats',
    ]);
    dd($stats['users']);

    expect($stats['users'][0]['stats'])->toHaveKeys([
        'used',
        'free',
        'total',
        'hits',
    ]);
});
