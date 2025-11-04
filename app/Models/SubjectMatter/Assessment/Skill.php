<?php

namespace App\Models\SubjectMatter\Assessment;

use App\Casts\Learning\Rubric;
use App\Classes\Ai\RubricSchema;
use App\Interfaces\AiPromptable;
use App\Models\Ai\AiPrompt;
use App\Models\Ai\AiSystemPrompt;
use App\Models\SubjectMatter\Subject;
use App\Models\SystemTables\Level;
use App\Traits\HasFullTextSearch;
use App\Traits\HasLevels;
use App\Traits\IsAiPromptable;
use App\View\Components\Assessment\RubricViewer;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class Skill extends Model implements AiPromptable
{
	use HasLevels, IsAiPromptable, HasFullTextSearch;
	
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['levels', 'subjects'];
	protected $table = "skills";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'global',
			'designation',
			'name',
			'description',
		];
	
	
	public function subjects(): BelongsToMany
	{
		return $this->belongsToMany(Subject::class, 'skills_subjects', 'skill_id', 'subject_id');
	}
	
	public function categories(): BelongsToMany
	{
		return $this->belongsToMany(SkillCategory::class, 'skill_category_designation', 'skill_id',
			'category_id')
		            ->withPivot(['designation'])
		            ->as('designation');
	}
	
	public function canActivate(): bool
	{
		return ($this->rubric != null);
	}
	
	protected function casts(): array
	{
		return
			[
				'description' => AsStringable::class,
				'rubric' => Rubric::class,
				'active' => 'boolean',
				'global' => 'boolean',
			];
	}
	
	public function canDelete(): bool
	{
		return true;
	}
	
	public function prettyName(): string
	{
		if($this->designation && !$this->name)
			return $this->designation;
		if(!$this->designation && $this->name)
			return $this->name;
		return $this->name . " (" . $this->designation . ")";
	}
	
	public function isGlobal(): bool
	{
		return $this->global;
	}
	
	
	/*********************************************************
	 * SCOPES
	 */
	#[Scope]
	protected function active($query): void
	{
		$query->where('active', true);
	}
	
	#[Scope]
	protected function global(Builder $query): void
	{
		$query->where('global', true);
	}
	
	#[Scope]
	protected function specific(Builder $query): void
	{
		$query->where('global', false);
	}
	
	#[Scope]
	protected function forLevels(Builder $query, array|int|Level|Collection $levels): void
	{
		if(is_array($levels) && (count($levels) == 0 || count($levels) == Level::count()))
			return;
		if(is_numeric($levels) && $levels == 0)
			return;
		if($levels instanceof Collection && ($levels->count() == 0 || $levels->count() == Level::count()))
			return;
		$level_ids = [];
		if($levels instanceof Collection)
		{
			$level_ids = $levels->map(function ($l)
			{
				if($l instanceof Level)
					return $l->id;
				if(is_numeric($l))
					return $l;
				return false;
			})->filter(fn($level_id) => $level_id)->toArray();
		}
		elseif($levels instanceof Level)
			$level_ids[] = $levels->id;
		elseif(is_numeric($levels))
			$level_ids[] = $levels;
		elseif(is_array($levels))
		{
			$level_ids = array_map(function ($l)
			{
				if($l instanceof Level)
					return $l->id;
				if(is_numeric($l))
					return $l;
				return null;
			}, $levels);
			$level_ids = array_filter($level_ids, fn($level_id) => ($level_id != null));
		}
		$query->whereHas('levels', function ($query) use ($level_ids)
		{
			$query->whereIn('system_tables.id', $level_ids);
		});
	}
	
	#[Scope]
	protected function forSubjects(Builder $query, array|int|Subject|Collection $subjects): void
	{
		if(is_array($subjects) && count($subjects) == 0)
			return;
		if(is_numeric($subjects) && $subjects == 0)
			return;
		if($subjects instanceof Collection && $subjects->count() == 0)
			return;
		$subject_ids = [];
		if($subjects instanceof Collection)
		{
			$subject_ids = $subjects->map(function ($l)
			{
				if($l instanceof Subject)
					return $l->id;
				if(is_numeric($l))
					return $l;
				return false;
			})->filter(fn($subject_id) => $subject_id)->toArray();
		}
		elseif($subjects instanceof Subject)
			$subject_ids[] = $subjects->id;
		elseif(is_int($subjects))
			$subject_ids[] = $subjects;
		elseif(is_array($subjects))
		{
			$subject_ids = array_map(function ($l)
			{
				if($l instanceof Subject)
					return $l->id;
				if(is_numeric($l))
					return $l;
				return null;
			}, $subjects);
			$subject_ids = array_filter($subject_ids, fn($subject_id) => ($subject_id != null));
		}
		$query->whereHas('subjects', function ($query) use ($subject_ids)
		{
			$query->whereIn('subjects.id', $subject_ids);
		});
	}
	
	/*********************************************************
	 * AI FUNCTIONS
	 */
	
	public static function availableProperties(): array
	{
		return [ 'rubric' => __('ai.prompt.skills.rubric')];
	}
	
	public static function defaultPrompt(string $property): string
	{
		return file_get_contents(resource_path('views/ai/prompts/skill/prompt.blade.php'));
	}
	
	public static function defaultSystemPrompt(string $property): string
	{
		return file_get_contents(resource_path('views/ai/prompts/skill/system.blade.php'));
	}
	
	public static function isStructured(string $property): bool
	{
		return true;
	}
	
	public static function defaultTools(string $property): array
	{
		return [];
	}
	
	public static function availableTokens(string $property): array
	{
		return
			[
				'{!! $skill_name !!}' => __('subjects.skills.name'),
				'{!! $skill_levels !!}' => trans_choice('subjects.skills.level', 2),
				'{!! $skill_is_global !!}' => __('subjects.skills.global'),
				'{!! $skill_subjects !!}' => __('subjects.skills.subject'),
				'{!! $skill_description !!}' => __('subjects.skills.description'),
			];
	}
	
	public function withTokens(): array
	{
		return
			[
				'skill_name' => $this->prettyName(),
				'skill_levels' => $this->levels->pluck('name')->join(', '),
				'skill_is_global' => $this->isGlobal()? __('subjects.skills.global.yes'): __('subjects.skills.global.no'),
				'skill_subjects' => $this->subjects->pluck('name')->join(', '),
				'skill_description' => $this->description,
			];
	}
	
	public static function getSchemaClass(string $property): string
	{
		return RubricSchema::class;
	}
	
	public function fillMockup(AiPrompt $prompt): string
	{
		$rubricViewer = new RubricViewer($this->fillRubric($prompt->last_results));
		return Blade::renderComponent($rubricViewer);
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
	
	public static function defaultTemperature(string $property): float
	{
		return 0.3;
	}
}
