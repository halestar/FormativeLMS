<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\FollowLinkTool;
use App\Mcp\Tools\GetLearningDemonstrationTool;
use App\Mcp\Tools\GetSkillInformationTool;
use App\Mcp\Tools\GreetTool;
use App\Mcp\Tools\SearchSkillsTool;
use App\Mcp\Tools\WorkFileAccessor;
use Laravel\Mcp\Server;

class FabLmsAiLearning extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'FABLms Ai Learning';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = <<<'MARKDOWN'
        Instructions describing how to use the server and its features.
    MARKDOWN;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        FollowLinkTool::class,
        SearchSkillsTool::class,
        GetSkillInformationTool::class,
        GetLearningDemonstrationTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
