<?php

namespace App\Mcp\Tools;

use App\Classes\Learning\DemonstrationQuestion;
use App\Classes\Learning\UrlResource;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetLearningDemonstrationTool extends Tool
{
	protected string $name = 'get-learning-demonstration';
	protected string $title = 'Get Learning Demonstration';
	/**
	 * The tool's description.
	 */
	protected string $description = <<<'MARKDOWN'
        This tool gets the information about a Learning Demonstration in the system given an ID.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): array|Response
    {
	    $data = $request->validate(
		    [
			    'id' => 'required|exists:learning_demonstration_templates,id|uuid',
		    ]);
	    $ld = LearningDemonstrationTemplate::find($data['id']);
	    if(!$ld)
		    return Response::error('The Learning Demonstration with the given ID does not exist.');
	    $response = [];
	    $response[] = Response::text('The Learning Demonstration with ID ' . $ld->id . ' has the following information: ');
	    $response[] = Response::text('Name: ' . $ld->name);
	    $response[] = Response::text('Abbreviation: ' . $ld->abbr);
	    $response[] = Response::text('Description: ' . $ld->demonstration);
		if($ld->workFiles()->count() > 0)
			$response[] = Response::text('The Learning Demonstration has the following work files: "' .
			                             $ld->workFiles->pluck('name')->implode('", "') . '"');

		$response[] = Response::text('It is linked to the ' . $ld->course->name . ' course.');
		if($ld->skills()->count() > 0)
			$response[] = Response::text('It is linked to the following skills: ' .
		                             $ld->skills->map(fn(Skill $skill) => "Skill: " . $skill->name . " (id: " . $skill->id . ")")->implode(', '));
		else
			$response[] = Response::text('It is not linked to any skills.');
		if(count($ld->links) > 0)
			$response[] = Response::text('It has the following URLs linked to it: ' .
			                             implode(", ", array_map(fn(UrlResource $url) => $url->title . "[" . $url->url . "]", $ld->links)));
	    if(count($ld->questions) > 0)
			$response[] = Response::text('It has the following Guiding Questions linked to it: ' .
			                             implode("\n",
			                                     array_map(fn(DemonstrationQuestion $question) => "Question: " . $question->question .
			                                                                                      ", Type: " . DemonstrationQuestion::typeOptions()[$question->type] .
			                                                                                      ($question->hasOptions()? "Options: " . implode(", ", $question->options) : ""),
				                                     $ld->questions)));
		$postingOptions = [];
		$postingOptions[] = "Allow Rating: " . ($ld->allow_rating ? "Yes" : "No");
	    $postingOptions[] = "Online Submission: " . ($ld->online_submission ? "Yes" : "No");
	    $postingOptions[] = "Online Submission: " . ($ld->online_submission ? "Yes" : "No");
	    $postingOptions[] = "Open Submission: " . ($ld->open_submission ? "Yes" : "No");
	    $postingOptions[] = "Submit After Due: " . ($ld->submit_after_due ? "Yes" : "No");
	    $postingOptions[] = "Share Submissions: " . ($ld->share_submissions ? "Yes" : "No");
		$response[] = Response::text('The following posting options are available: ' . implode(', ', $postingOptions));
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
				'id' => $schema->string()
				               ->description('The ID of the Learning Demonstration to get information for. The ID is a UUID string with dashes.')
				               ->required(),
			];
	}
}
