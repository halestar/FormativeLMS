<?php

namespace App\Livewire\Assessment;

use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SubjectMatter\Course;
use App\Models\SystemTables\Level;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

class SkillSelector extends Component
{
	public Person $person;
	public Course $course;
	public string|int $selected = 'suggested';
	public string $search = '';
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
		// we will base the levels based on the suggested skills
		$this->filterLevels = $this->course->campus->levels->pluck('id')
		                                                   ->toArray();
		$this->selectedSkills = count($this->selectedSkillIds) > 0 ?
			Skill::whereIn('id', $this->selectedSkillIds)->get() : new Collection;
	}

	#[On('skill-selector-category.select-category')]
	public function setSelectedCategory(?int $selectedCategoryId = null)
	{
		$this->selected = $selectedCategoryId ?? 'suggested';
	}

	#[Computed]
	public function results(): Collection
	{
		if ($this->search && strlen($this->search) > 2)
		{
			return Skill::search($this->search)
			            ->where('active', true)
			            ->whereIn(
				            'levels', array_map(fn (
					            $levelId) => $this->levels->find($levelId)->name, $this->filterLevels)
			            )
			            ->whereNotIn('id', $this->selectedSkillIds)
			            ->get();
		}
		if ($this->selected == 'suggested')
		{
			$suggested = $this->course->suggestedSkills($this->selectedSkillIds);
			if (count($this->filterLevels) > 0 && count($this->filterLevels) < $this->levels->count())
			{
				return $suggested->filter(function (Skill $skill)
				{
					return $skill->levels()
					             ->whereIn('system_tables.id', $this->filterLevels)
					             ->count() > 0;
				});
			}
			return $suggested;
		}
		elseif ($this->selected == 'subject')
		{
			return Skill::active()
			            ->specific()
			            ->forSubjects($this->course->subject_id)
			            ->forLevels($this->filterLevels)
			            ->whereNotIn('id', $this->selectedSkillIds)
			            ->get();
		}
		elseif ($this->selected == 'global')
		{
			return Skill::global()
			            ->active()
			            ->forLevels($this->filterLevels)
			            ->whereNotIn('id', $this->selectedSkillIds)
			            ->get();
		}
		elseif (is_numeric($this->selected))
		{
			$cat = SkillCategory::find($this->selected);
			return $cat->skills()
			           ->active()
			           ->forLevels($this->filterLevels)
			           ->whereNotIn('id', $this->selectedSkillIds)
			           ->get();
		}
		return new Collection;
	}

	public function setSuggested()
	{
		$this->selected = 'suggested';
	}

	public function setSubject()
	{
		$this->selected = 'subject';
	}

	public function setGlobal()
	{
		$this->selected = 'global';
	}

	public function addSkill(Skill $skill)
	{
		if (!$this->selectedSkills->contains(fn (Skill $s) => $s->id == $skill->id))
		{
			$this->selectedSkills->push($skill);
			$this->selectedSkillIds[] = $skill->id;
			$this->dispatch('skill-selector.skills-added', skill: $skill->id);
		}
	}

	public function removeSkill(Skill $skill)
	{
		$this->selectedSkills = $this->selectedSkills->filter(fn (Skill $s) => $s->id != $skill->id);
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

	#[Computed]
	public function filteredLevels(): string
	{
		return implode(', ', array_map(fn ($levelId) => $this->levels->find($levelId)->name, $this->filterLevels));
	}
}
