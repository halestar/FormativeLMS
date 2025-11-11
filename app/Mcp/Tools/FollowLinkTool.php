<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class FollowLinkTool extends Tool
{
    protected string $name = 'follow-url';
    protected string $title = 'Follow Link';
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        This tool will return the contents of the URL provided.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate(
        [
            'url' => 'required|url',
        ]);
        $response = Http::get($validated['url']);
        if($response->failed())
            return Response::error('Failed to fetch the URL.');
        return Response::text($response->body());
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()
                            ->format('uri')
                            ->description('The URL to follow and get the contents of.')
                            ->required(),
        ];
    }
}
