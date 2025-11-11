<?php

namespace App\Mcp\Tools;

use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SystemTables\Level;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class SearchSkillsTool extends Tool
{
    protected string $name = 'search-skills';
    protected string $title = 'Search Skills';
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Search for Skills in the school by keyword.
    MARKDOWN;

    /**
     * Handle the tool request.
     * @return array<int, \Laravel\Mcp\Response>
     */
    public function handle(Request $request): array|Response
    {
        $data = $request->validate(
		[
			'keyword' => 'required|string|min:3',
		]);
		$results = Skill::search($data['keyword'])->get();
		if($results->count() == 0)
			return Response::text('No skills were found.');
		$response = [];
		$response[] = Response::text('The following skills were found:');
		foreach($results as $result)
		{
			$response[] = Response::text("The skill " . $result->prettyName() . " with id " . $result->id .
			                             " was found and it has the following description: " .
										 $result->description);
		}
        return $response;
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return
        [
            'keyword' => $schema->string()
                                ->min(3)
                                ->description('The keyword to search for.')
                                ->required(),
        ];
    }
}
