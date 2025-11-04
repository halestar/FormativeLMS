<?php

namespace App\Console\Commands;

use App\Casts\Learning\Rubric;
use App\Interfaces\HasRubric;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\Utilities\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class DevelopRubrics extends Command
{
	public static string $backupDirectory = 'backups/develop-rubrics';
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fablms:develop-rubrics';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This command is used to develop rubrics for FAB LMS. It is AI-based and will take your existing Skills (both knowledge and character) and will generate a rubric for them.';
	private string $settingName = 'commands.develop-rubrics';
	
	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		//grab the setting or create one
		$developRubricSetting = SystemSetting::where('name', $this->settingName)
		                                     ->first();
		if(!$developRubricSetting)
		{
			$developRubricSetting = new SystemSetting();
			$developRubricSetting->name = $this->settingName;
			$developRubricSetting->value =
				[
					'character_index' => -1,
					'knowledge_index' => -1,
					'total_completed' => 0,
					'character_skills' => 0,
					'knowledge_skills' => 0,
				];
			$developRubricSetting->save();
		}
		
		$systemPromptVars =
			[
				'max_points' => config('lms.rubric_max_points'),
				'max_criteria' => 10,
				'max_skills_per_criteria' => 5
			];
		try
		{
			$settingVals = $developRubricSetting->value;
			$this->info('Statistics for this run:' . print_r($settingVals, true));
			foreach(Skill::where('id', '>', $developRubricSetting->value['knowledge_index'])
			             ->get() as $skill)
			{
				$this->info("Developing rubric for knowledge skill: " . $skill->designation);
				$this->info("Asking Ai...");
				$response = Prism::structured()
				                 ->using(Provider::Gemini, 'gemini-2.0-flash')
				                 ->withSystemPrompt(view('ai.default-prompts.skill.system-prompt',
					                 $systemPromptVars))
				                 ->withPrompt(view('ai.default-prompts.skill.prompt', ['skill' => $skill]))
				                 ->withSchema($this->getRubricSchema())
				                 ->asStructured();
				$this->info("Response received. Updating Skill...");
				$rubric = $this->decodeRubric($response->structured);
				$skill->rubric = $rubric;
				$skill->save();
				$this->info("Skill updated. Making a backup");
				if(!$this->backup($skill))
				{
					$this->error("Error making backup. Figure out what went wrong");
					return;
				}
				$this->info("Backup complete, moving to the next skill.");
				//finally, update the setting
				$settingVals['knowledge_index'] = $skill->id;
				$settingVals['knowledge_skills']++;
				$settingVals['total_completed']++;
				$developRubricSetting->value = $settingVals;
				$developRubricSetting->save();
			}
		}
		catch(PrismException $e)
		{
			$this->error("Error: " . $e->getMessage());
			return;
		}
		$this->info("All Skills have been updated");
	}
	
	private function getRubricSchema(): ObjectSchema
	{
		$levelOfPerformanceSchema = [new StringSchema('description',
			'The description of the criterion detailing what it is evaluating.')];
		$reqFields = ['description'];
		for($i = 0; $i < config('lms.rubric_max_points'); $i++)
		{
			$levelOfPerformanceSchema[] = new StringSchema
			(
				name: "pts." . $i,
				description: 'The description of the level of performance worth ' . $i . ' points.'
			);
			$reqFields[] = "pts." . $i;
		}
		return new ObjectSchema
		(
			name: 'rubric',
			description: 'A rubric for a skill',
			properties: [
				new ArraySchema
				(
					name: 'criteria',
					description: 'A criterion for the rubric',
					items: new ObjectSchema
					(
						name: 'criterion',
						description: 'A criterion for the rubric, containing a description for each level of performance.',
						properties: $levelOfPerformanceSchema,
						requiredFields: $reqFields
					),
				)
			],
			requiredFields: ['criteria']
		);
	}
	
	private function decodeRubric(array $rubric): Rubric
	{
		$points = [];
		for($i = 0; $i < config('lms.rubric_max_points'); $i++)
			$points[] = $i;
		$criteria = [];
		$descriptions = [];
		foreach($rubric['criteria'] as $criterion)
		{
			$criteria[] = $criterion['description'];
			$descriptionRow = [];
			for($i = 0; $i < config('lms.rubric_max_points'); $i++)
				$descriptionRow[] = $criterion['pts.' . $i];
			$descriptions[] = $descriptionRow;
		}
		$data =
			[
				'points' => $points,
				'criteria' => $criteria,
				'descriptions' => $descriptions,
			];
		return Rubric::hydrate($data);
	}
	
	public function backup(HasRubric $skill): bool
	{
		return Storage::disk('private')
		              ->put(DevelopRubrics::$backupDirectory . "/" . $skill->getSkillName() . ".json",
			              json_encode($skill->getRubric()
			                                ->toArray()));
	}
}
