<?php

namespace App\Livewire\Assessment;

use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SubjectMatter\Course;
use App\Models\SystemTables\Level;
use Illuminate\Support\Collection;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class SkillSelector extends Component
{
	public Person $person;
	public Course $course;
	public string|int $selected = "suggested";
	public string $search = '';
	public ?Collection $results = null;
	public Collection $levels;
	public array $filterLevels = [];
	public Collection $rootCategories;
	#[Modelable]
	public array $selectedSkillIds = [];
	public Collection $selectedSkills;
	
	public function mount(Course $course)
	{
		$this->person = auth()->user();
		$this->course = $course;
		$this->rootCategories = SkillCategory::root()
		                                     ->get();
		$this->levels = Level::all();
		//we will base the levels based on the suggested skills
		$this->filterLevels = $this->course->campus->levels->pluck('id')
		                                                   ->toArray();
		$this->selectedSkills = count($this->selectedSkillIds) > 0 ?
			Skill::whereIn('id', $this->selectedSkillIds)->get(): new Collection();
	}
	
	#[On('skill-selector-category.select-category')]
	public function setSelectedCategory(?int $selectedCategoryId = null)
	{
		$this->selected = $selectedCategoryId ?? "suggested";
	}
	
	public function determineContent()
	{
		if($this->selected == "suggested")
		{
			$this->results = $this->course->suggestedSkills($this->selectedSkillIds);
			if(count($this->filterLevels) > 0 && count($this->filterLevels) < $this->levels->count())
			{
				$this->results = $this->results->filter(function(Skill $skill)
				{
					return $skill->levels()
					             ->whereIn('system_tables.id', $this->filterLevels)
					             ->count() > 0;
				});
			}
		}
		elseif($this->selected == "subject")
			$this->results = Skill::active()
			                      ->specific()
			                      ->forSubjects($this->course->subject_id)
			                      ->forLevels($this->filterLevels)
			                      ->whereNotIn('id', $this->selectedSkillIds)
			                      ->get();
		elseif($this->selected == "global")
			$this->results = Skill::global()
			                      ->active()
			                      ->forLevels($this->filterLevels)
			                      ->whereNotIn('id', $this->selectedSkillIds)
			                      ->get();
		elseif(is_numeric($this->selected))
		{
			$cat = SkillCategory::find($this->selected);
			$this->results = $cat->skills()
			                     ->active()
			                     ->forLevels($this->filterLevels)
			                     ->whereNotIn('id', $this->selectedSkillIds)
			                     ->get();
		}
		else
			$this->results = null;
	}
	
	public function setSuggested()
	{
		$this->selected = "suggested";
	}
	
	public function setSubject()
	{
		$this->selected = "subject";
	}
	
	public function setGlobal()
	{
		$this->selected = "global";
	}
	
	public function addSkill(Skill $skill)
	{
		if(!$this->selectedSkills->contains(fn(Skill $s) => $s->id == $skill->id))
		{
			$this->selectedSkills->push($skill);
			$this->selectedSkillIds [] = $skill->id;
			$this->dispatch('skill-selector.skills-added', skill: $skill->id);
		}
	}
	
	function removeSkill(Skill $skill)
	{
		$this->selectedSkills = $this->selectedSkills->filter(fn(Skill $s) => $s->id != $skill->id);
		$this->selectedSkillIds = $this->selectedSkills->pluck('id')->toArray();
		$this->dispatch('skill-selector.skills-removed', skill: $skill->id);
	}
	
	public function toggleAllLevels(bool $state)
	{
		$this->filterLevels = $state ? Level::all()
		                                    ->pluck('id')
		                                    ->toArray() : [];
	}
	
	public function render()
	{
		if($this->search && strlen($this->search) > 2)
		{
			$this->results = Skill::search($this->search)
			                      ->active()
			                      ->forLevels($this->filterLevels)
			                      ->get();
		}
		else
			$this->determineContent();
		return view('livewire.assessment.skill-selector');
	}
	
	#[On('skill-selector.set-course')]
	public function setCourse(Course $course)
	{
		$this->course = $course;
		$this->filterLevels = $this->course->campus->levels->pluck('id')
		                                                   ->toArray();
		$this->setSuggested();
	}
}
