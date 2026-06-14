<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\NewShortenerTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Shortener Server')]
#[Version('1.0.0')]
#[Instructions('Create URL shorteners by generating compact, unique aliases for long URLs.')]
class ShortenerServer extends Server
{
    public string $serverName = 'Shortener Server';

    public string $serverVersion = '0.0.1';

    public string $instructions = 'Create URL shorteners by generating compact, unique aliases for long URLs.';

    public array $tools = [
        NewShortenerTool::class,
    ];

    public array $resources = [
        // ExampleResource::class,
    ];

    public array $prompts = [
        // ExamplePrompt::class,
    ];
}
