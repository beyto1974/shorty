<?php

namespace App\Mcp\Tools;

use App\Actions\Shortener\ShortenerCreateAction;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;

#[Description('Generates a compact URL shortener alias from a long URL for easier sharing and tracking.')]
#[IsOpenWorld(true)]
#[IsIdempotent(false)]
class NewShortenerTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->get('url');

        $shortener = ShortenerCreateAction::run([
            'original_url' => $url,
        ], auth()->user());

        // TODO structured response not working (invalid response - MCP Inspector)
        return Response::text(
            sprintf("Original URL: %s\nShorter URL: %s", $url, $shortener->getRedirectUrl())
        );
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()
                ->description('The URL to be shortened.')
                ->required(),

        ];
    }
}
