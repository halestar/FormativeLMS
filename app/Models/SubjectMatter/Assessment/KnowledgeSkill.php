<?php

namespace App\Models\SubjectMatter\Assessment;

use App\Casts\Rubric;
use App\Interfaces\AiPromptable;
use App\Interfaces\HasRubric;
use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use App\Models\SubjectMatter\Subject;
use App\Traits\IsAiPromptable;
use App\Traits\Leveable;
use App\View\Components\Assessment\RubricViewer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Blade;
use Laravel\Scout\Searchable;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class KnowledgeSkill extends Model implements HasRubric, AiPromptable
{
	use Searchable, Leveable, IsAiPromptable;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['subject'];
	protected $table = "knowledge_skills";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'subject_id',
			'designation',
			'name',
			'description',
		];
	
	public static function promptDescription(): string
	{
		return __('ai.prompt.skills.knowledge.description');
	}
	
	public function toSearchableArray(): array
	{
		return
			[
				'designation' => $this->designation,
				'name' => $this->name,
				'description' => $this->description,
			];
	}
	
	public function subject(): BelongsTo
	{
		return $this->belongsTo(Subject::class);
	}
	
	public function categories(): MorphToMany
	{
		return $this->morphToMany(SkillCategory::class, 'skill', 'skill_category_designation', 'skill_id',
			'category_id')
		            ->withPivot(['designation_id'])
		            ->as('info')
		            ->using(SkillCategoryDesignation::class);
	}
	
	public function canActivate(): bool
	{
		return ($this->rubric != null);
	}
	
	public function getRubric(): ?Rubric
	{
		return $this->rubric;
	}
	
	public function setRubric(Rubric $rubric)
	{
		$this->rubric = $rubric;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function getSkillId(): int
	{
		return $this->id;
	}
	
	public function getSkillName(): string
	{
		return $this->name ?? $this->designation;
	}
	
	public function getDefaultPrompt(bool $overwrite = false): AiPrompt
	{
		//do we have one?
		if($this->ai_prompts()
		        ->where('person_id', null)
		        ->exists())
		{
			if(!$overwrite)
				return $this->ai_prompts()
				            ->where('person_id', null)
				            ->first();
			else
				$defaultPrompt = $this->ai_prompts()
				                      ->where('person_id', null)
				                      ->first();
		}
		else
			$defaultPrompt = new AiPrompt();
		$defaultPrompt->prompt = Blade::render('ai.default-prompts.knowledge-skill.prompt', ['skill' => $this]);
		$defaultPrompt->structured = true;
		$defaultPrompt->ai_promptable()
		              ->associate($this);
		$defaultPrompt->systemPrompt()
		              ->associate($this->getDefaultSystemPrompt());
		$defaultPrompt->save();
		return $defaultPrompt;
	}
	
	public function getDefaultSystemPrompt(bool $overwrite = false): AiSystemPrompt
	{
		$systemPrompt = AiSystemPrompt::whereNull('person_id')
		                              ->where('className', static::class)
		                              ->first();
		if($systemPrompt && !$overwrite)
			return $systemPrompt;
		if(!$systemPrompt)
			$systemPrompt = new AiSystemPrompt();
		$systemPrompt->className = static::class;
		$systemPrompt->prompt = Blade::render('ai.default-prompts.knowledge-skill.system-prompt');
		$systemPrompt->save();
		return $systemPrompt;
	}
	
	/**
	 * In this case, we return the schema for the rubric for this skill
	 * @return ObjectSchema
	 */
	public function getAiSchema(): ObjectSchema
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
	
	public function aiFill(AiPrompt $prompt): void
	{
		$this->rubric = $this->fillRubric($prompt->last_results);
	}
	
	private function fillRubric(array $data): ?Rubric
	{
		if(count($data['criteria']) == 0)
			return null;
		//max points
		$maxPoints = (count($data['criteria'][0])) - 1;
		$points = [];
		for($i = 0; $i < $maxPoints; $i++)
			$points[] = $i;
		$criteria = [];
		$descriptions = [];
		foreach($data['criteria'] as $criterion)
		{
			$criteria[] = $criterion['description'];
			$descriptionRow = [];
			for($i = 0; $i < $maxPoints; $i++)
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
	
	public function getEditableName(): string
	{
		return __('ai.editable.skill.knowledge', ['name' => $this->name]);
	}
	
	public function getBreacrumb(): array
	{
		return
			[
				trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
				$this->designation => route('subjects.skills.show.knowledge', $this),
				__('subjects.skills.rubric.builder') => route('subjects.skills.rubric.knowledge', $this),
			];
	}
	
	public function fillMockup(AiPrompt $prompt): string
	{
		$rubricViewer = new RubricViewer($this->fillRubric($prompt->last_results));
		return Blade::renderComponent($rubricViewer);
	}
	
	protected function casts(): array
	{
		return
			[
				'rubric' => Rubric::class,
				'active' => 'boolean',
			];
	}
}
