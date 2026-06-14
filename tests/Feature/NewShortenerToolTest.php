<?php

use App\Mcp\Servers\ShortenerServer;
use App\Mcp\Tools\NewShortenerTool;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::orderBy('id')->first());
});

it('can use new shortener tool', function () {
    $response = ShortenerServer::tool(NewShortenerTool::class, [
        'url' => 'http://www.duckduckgo.com',
    ]);

    $response->assertOk();

    $response->assertName('new-shortener-tool');

    $response->assertSee('Original URL:');
    $response->assertSee('Shorter URL:');
});
