<?php

namespace App\Mcp\Tools;

use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetSkillInformationTool extends Tool
{
	protected string $name = 'get-skill-information';
	protected string $title = 'Get Skill Information';
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        This tool gets the information about a Skill in the system given a skill ID.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): array|Response
    {
        $data = $request->validate(
            [
                'id' => 'required|exists:skills,id|integer|min:1',
            ],
            [
				'id.*' => 'The skill with the given ID does not exist.',
            ]);
		$skill = Skill::find($data['id']);
		if(!$skill)
			return Response::error('The skill with the given ID does not exist.');
		$response = [];
		$response[] = Response::text('The skill with ID ' . $skill->id . ' has the following information: ');
		if($skill->name)
			$response[] = Response::text('Name: ' . $skill->name . ', ');
		if($skill->designation)
			$response[] = Response::text('Description: ' . $skill->description . ', ');
		if($skill->global)
			$response[] = Response::text("This skill is global.");
		$response[] = Response::text('The skill is applicable to the following grades: ' . $skill->levels->pluck('name')->implode(', '));
		$response[] = Response::text('The skill is applicable to the following subjects: ' . $skill->subjects->pluck('name')->implode(', '));
		$response[] = Response::text('The skill is linked to the following categories: ' .
		                             $skill->categories->map(fn(SkillCategory $category) => $category->getCategoryPath())->implode(', '));
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
            'id' => $schema->integer()->description('The ID of the skill to get information for.')->required(),
        ];
    }
}
