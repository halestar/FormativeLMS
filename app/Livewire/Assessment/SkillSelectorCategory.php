<?php

namespace App\Livewire\Assessment;

use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class SkillSelectorCategory extends Component
{
	public SkillCategory $category;
	public bool $open = false;
	public bool $selected = false;
	public ?SkillCategory $selectedCategory = null;
	public ?Collection $children = null;
	public int $numChildren;
	
	public function mount(SkillCategory $category)
	{
		$this->category = $category;
		$this->children = null;
		$this->numChildren = $this->category->subCategories()->count();
	}
	
	public function toggleCategory()
	{
		$this->open = !$this->open;
		if($this->open && !$this->children)
			$this->children = $this->category->subCategories;
		else
			$this->children = null;
	}
	
	public function selectCategory()
	{
		$this->selected = !$this->selected;
		$this->dispatch('skill-selector-category.select-category', selectedCategoryId: $this->selected ? $this->category->id : null);
	}
	
	#[On('skill-selector-category.select-category')]
	public function receiveSelectCategory(?int $selectedCategoryId = null)
	{
		if($selectedCategoryId != $this->category->id)
			$this->selected = false;
	}
	
	
    public function render()
    {
        return view('livewire.assessment.skill-selector-category');
    }
}
