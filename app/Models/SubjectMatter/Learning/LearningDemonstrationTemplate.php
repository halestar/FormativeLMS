<?php

namespace App\Models\SubjectMatter\Learning;

use App\Casts\Learning\AsDemonstrationQuestions;
use App\Casts\Learning\AsUrlResources;
use App\Classes\AI\AiSchema;
use App\Classes\Learning\DemonstrationQuestion;
use App\Classes\Learning\UrlResource;
use App\Enums\AiSchemaType;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\AiPromptable;
use App\Interfaces\Fileable;
use App\Models\Ai\AiPrompt;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\Scopes\OrdeByNameScope;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Course;
use App\Models\Utilities\WorkFile;
use App\Traits\HasWorkFiles;
use App\Traits\IsAiPromptable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;

#[ScopedBy(OrdeByNameScope::class)]
class LearningDemonstrationTemplate extends Model implements Fileable, AiPromptable
{
	use HasUuids, isAiPromptable, HasWorkFiles;
	public $timestamps = true;
	public $incrementing = false;
	protected $table = "learning_demonstration_templates";
	protected $primaryKey = "id";
	protected $keyType = 'string';
	protected $fillable =
		[
			'name',
			'abbr',
			'demonstration',
			'allow_rating',
			'online_submission',
			'open_submission',
			'submit_after_due',
			'share_submissions',
			'shareable',
		];
	
	protected function casts(): array
	{
		return
			[
				'links' => AsUrlResources::class,
				'questions' => AsDemonstrationQuestions::class,
				'allow_rating' => 'boolean',
				'online_submission' => 'boolean',
				'open_submission' => 'boolean',
				'submit_after_due' => 'boolean',
				'share_submissions' => 'boolean',
				'shareable' => 'boolean',
			];
	}
	
	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::LearningDemonstrationWork;
	}
	
	public function shouldBePublic(): bool
	{
		return false;
	}
	
	public function owner(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	
	public function course(): BelongsTo
	{
		return $this->belongsTo(Course::class, 'course_id');
	}
	
	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationTemplate::class, 'created_by');
	}

	public function learningDemonstrations(): HasMany
	{
		return $this->hasMany(LearningDemonstration::class, 'template_id');
	}
	
	public function canShare(): bool
	{
		return $this->created_by == null;
	}
	
	public function type(): BelongsTo
	{
		return $this->belongsTo(LearningDemonstrationType::class, 'type_id');
	}
	
	public function skills(): BelongsToMany
	{
		return $this->belongsToMany(Skill::class, 'learning_demonstration_template_skills',
			'template_id', 'skill_id')
			->withPivot(['rubric', 'weight', 'id'])
			->as('assessment')
			->using(LearningDemonstrationTemplateSkill::class);
	}
	
	public function assessments(): HasMany
	{
		return $this->hasMany(LearningDemonstrationTemplateSkill::class, 'template_id');
	}

    #[Scope]
    protected function inCourse(Builder $query, Course|int $course): void
    {
        $query->where('course_id', $course instanceof Course ? $course->id : $course);
    }

    /**********************************************************************
     * AI FUNCTIONS
     **********************************************************************/

    public static function availableProperties(): array
    {
        return
        [
            'skills' => __('ai.prompt.ld.templates.skills'),
            'demonstration' => __('ai.prompt.ld.templates.demonstration'),
            'links' => __('ai.prompt.ld.templates.links'),
            'questions' => __('ai.prompt.ld.templates.questions'),
        ];
    }

    public static function defaultPrompt(string $property): string
    {
        switch($property)
        {
            case 'skills': return file_get_contents(view(self::PROMPT_VIEW_PATH . 'ld.skills')->getPath());
	        case 'demonstration': return file_get_contents(view(self::PROMPT_VIEW_PATH . 'ld.demonstration')->getPath());
	        case 'links': return file_get_contents(view(self::PROMPT_VIEW_PATH . 'ld.links')->getPath());
	        case 'questions': return file_get_contents(view(self::PROMPT_VIEW_PATH . 'ld.questions')->getPath());
        }
        return '';
    }

    public static function defaultSystemPrompt(string $property): string
    {
        return file_get_contents(view(self::PROMPT_VIEW_PATH . 'ld.system')->getPath());
    }

    public static function defaultTemperature(string $property): float
    {
        return 0.3;
    }

    public static function isStructured(string $property): bool
    {
        return false;
    }

    public static function availableTokens(string $property): array
    {
        return
        [
	        '{!! $demonstration_description !!}' => __('learning.demonstrations.demonstration'),
	        '{!! $demonstration_course !!}' => __('learning.demonstrations.course'),
	        '{!! $demonstration_id !!}' => __('learning.demonstrations.id'),
	        '{!! $demonstration_name !!}' => __('learning.demonstrations.name'),
	        '{!! $demonstration_abbr !!}' => __('learning.demonstrations.abbr'),
	        '{!! $demonstration_skills !!}' => __('learning.demonstrations.skills'),
	        '{!! $demonstration_links !!}' => __('learning.demonstrations.resources.url'),
	        '{!! $demonstration_questions !!}' => __('learning.demonstrations.questions'),
        ];
    }

    public static function getSchemaClass(string $property): ?AiSchema
    {
		return null;
    }

    public function fillMockup(AiPrompt $prompt): string
    {
        if($prompt->property == "skills")
        {
			//parse the text for skills
	        $lines = explode("\n", $prompt->last_results);
			$skills = [];
			$reasons = [];
			foreach($lines as $line)
			{
				//attempt match
				$matches = [];
				if(preg_match('/SKILL:\s*(.*),\s*ID:\s*(\d+)\s*,\s*REASON:\s*(.*)$/', $line, $matches))
				{
					//attempt to load the skill
					$skill = Skill::find($matches[2]);
					if($skill)
					{
						$skills[] = $skill;
						$reasons[$skill->id] = $matches[3];
					}
				}
			}
			return Blade::render('ai.prompts.ld.fill.skills', ['skills' => $skills, 'reasons' => $reasons, 'prompt' => $prompt]);
        }
		if($prompt->property == "demonstration")
			return $prompt->last_results;
	    if($prompt->property == "links")
	    {
		    //parse the text for skills
		    $lines = explode("\n", $prompt->last_results);
		    $links = [];
		    foreach($lines as $line)
		    {
			    //attempt match
			    $matches = [];
			    if(preg_match('/LINK:\s*(.*),\s*TITLE:\s*(.+)$/', $line, $matches))
				    $links[] = ['title' => $matches[2], 'url' => $matches[1]];
		    }
		    return Blade::render('ai.prompts.ld.fill.links', ['links' => $links, 'prompt' => $prompt]);
	    }
	    if($prompt->property == "questions")
	    {
		    //parse the text for skills
		    $lines = explode("\n", $prompt->last_results);
		    $questions = [];
		    foreach($lines as $line)
		    {
			    //attempt match
			    $matches = [];
			    if(preg_match('/QUESTION:\s*(.*)[,]?\s*TYPE:\s*(\d)(\s*[,]?\s*OPTIONS:\s*\[(.*)]\s*)?$/', $line, $matches))
			    {
					if($matches[2] != DemonstrationQuestion::TYPE_MULTIPLE && $matches[2] != DemonstrationQuestion::TYPE_CHOICE)
				        $questions[] = ['question' => $matches[1], 'type' => $matches[2], 'options' => []];
					else
					{
						$options = explode(',', $matches[4]);
						$questions[] = ['question' => $matches[1], 'type' => $matches[2], 'options' => $options];
					}
			    }
		    }
		    return Blade::render('ai.prompts.ld.fill.questions', ['questions' => $questions, 'prompt' => $prompt]);
	    }
		return nl2br($prompt->last_results);
    }

    public function aiFill(AiPrompt $prompt): void
    {
        if($prompt->property == "skills")
        {
	        $lines = explode("\n", $prompt->last_results);
	        $skills = [];
	        foreach($lines as $line)
	        {
		        //attempt match
		        $matches = [];
		        if(preg_match('/SKILL:\s*(.*),\s*ID:\s*(\d+)\s*,\s*REASON:\s*(.*)$/', $line, $matches))
		        {
			        //attempt to load the skill
			        $skill = Skill::find($matches[2]);
			        if($skill)
				        $skills[$skill->id] = ['rubric' => $skill->rubric];
		        }
	        }
			Log::debug("Skills to add: " . print_r($skills, true));
			if(count($skills) > 0)
				$this->skills()->syncWithoutDetaching($skills);
        }
		elseif($prompt->property == "demonstration")
		{
			$this->demonstration = $prompt->last_results;
			$this->save();
		}
		elseif($prompt->property == "links")
		{
			//parse the text for skills
			$lines = explode("\n", $prompt->last_results);
			$links = [];
			foreach($lines as $line)
			{
				//attempt match
				$matches = [];
				if(preg_match('/LINK:\s*(.*),\s*TITLE:\s*(.+)$/', $line, $matches))
					$links[] = ['title' => $matches[2], 'url' => $matches[1]];
			}
			$ldLinks = $this->links;
			foreach($links as $link)
				$ldLinks[] = new UrlResource($link['url'], $link['title']);
			$this->links = $ldLinks;
			$this->save();
		}
	    elseif($prompt->property == "questions")
	    {
		    //parse the text for skills
		    $lines = explode("\n", $prompt->last_results);
		    $questions = [];
		    foreach($lines as $line)
		    {
			    //attempt match
			    $matches = [];
			    if(preg_match('/QUESTION:\s*(.*)[,]?\s*TYPE:\s*(\d)(\s*[,]?\s*OPTIONS:\s*\[(.*)]\s*)?$/', $line, $matches))
			    {
				    $questions[] = DemonstrationQuestion::hydrate(
				    [
					    'question' => $matches[1],
					    'type' => $matches[2],
					    'options' => explode(',', $matches[4]?? '')?? []
				    ]);
			    }
		    }
			$this->questions = $questions;
			$this->save();
	    }
    }

    public function withTokens(): array
    {
        return
        [
	        'demonstration_description' => $this->demonstration,
	        'demonstration_abbr' => $this->abbr,
	        'demonstration_id' => $this->id,
	        'demonstration_name' => $this->name,
	        'demonstration_course' => $this->course->name,
	        'demonstration_skills' => $this->skills->map(fn(Skill $skill) => $skill->prettyName() . " (id: " . $skill->id . ")")->join(', '),
	        'demonstration_links' => implode(", ", array_map(fn(UrlResource $url) => $url->title . "[" . $url->url . "]", $this->links)),
	        'demonstration_questions' => implode("\n",
		        array_map(fn(DemonstrationQuestion $question) => "Question: " . $question->question .
		                                                     ", Type: " . DemonstrationQuestion::typeOptions()[$question->type] .
		                                                     ($question->hasOptions()? "Options: " . implode(", ", $question->options) : ""),
		        $this->questions)),
        ];
    }
}
