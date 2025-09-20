<?php

namespace App\Livewire\Assessment;

use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\KnowledgeSkill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class SkillCategoryBrowser extends Component
{
	public Collection $rootCategories;
	public ?SkillCategory $selectedCategory = null;
	public ?SkillCategory $editingCategory = null;
	public bool $editingDescription = false;
	public array $openTo;
	public string $search = '';
	public ?Collection $knowledgeResults = null;
	public ?Collection $characterResults = null;
	
	public function mount()
	{
		$this->rootCategories = SkillCategory::root()
		                                     ->get();
		$this->openTo = auth()->user()->prefs->get('skill-category-browser-open-to', []);
		//is there a selected category?
		$selected_id = null;
		foreach($this->openTo as $category => $state)
		{
			if($state['selected'])
			{
				$selected_id = $category;
				break;
			}
		}
		if($selected_id)
			$this->selectedCategory = SkillCategory::find($selected_id);
	}
	
	public function createCategory()
	{
		$newCategory = new SkillCategory();
		$newCategory->name = __('subjects.skills.category.new');
		$newCategory->parent_id = $this->selectedCategory->id ?? null;
		$newCategory->save();
		if(!$this->selectedCategory)
		{
			$this->rootCategories->push($newCategory);
			$this->editingCategory = $newCategory;
			$this->dispatch('edit-category', editCategoryId: $newCategory->id)
			     ->to(CategoryItem::class);
		}
		else
			$this->dispatch('new-category', parentId: $newCategory->parent_id)
			     ->to(CategoryItem::class);
	}
	
	#[On('edit-category')]
	public function setEditCategory(?int $editCategoryId)
	{
		if($editCategoryId == null)
		{
			$this->editingCategory = null;
			$this->dispatch('edit-category', editCategoryId: null)
			     ->to(CategoryItem::class);
			
		}
		else
		{
			$this->editingCategory = SkillCategory::find($editCategoryId);
			$this->dispatch('edit-category', editCategoryId: $this->editingCategory->id)
			     ->to(CategoryItem::class);
		}
	}
	
	#[On('select-category')]
	public function setSelectedCategory(?int $selectedCategoryId)
	{
		//get the prefs
		$self = auth()->user();
		$prefs = $self->prefs->get('skill-category-browser-open-to', []);
		//first, we unselect the old state.
		if($this->selectedCategory && isset($prefs[$this->selectedCategory->id]))
		{
			$prefs[$this->selectedCategory->id]['selected'] = false;
			if(!$prefs[$this->selectedCategory->id]['open'] && !$prefs[$this->selectedCategory->id]['selected'])
				unset($prefs[$this->selectedCategory->id]);
		}
		//cnext, we need to open the new category.
		if(isset($prefs[$selectedCategoryId]))
		{
			//there is, so we manipulate it.
			$prefs[$selectedCategoryId]['selected'] = $selectedCategoryId ?? null;
			if(!$prefs[$selectedCategoryId]['open'] && !$prefs[$selectedCategoryId]['selected'])
				unset($prefs[$selectedCategoryId]);
		}
		elseif($selectedCategoryId)
		{
			//no entry, we only worry about it if we are selecting.
			$prefs[$selectedCategoryId] = ['open' => false, 'selected' => true];
		}
		$self->prefs->set('skill-category-browser-open-to', $prefs);
		$self->save();
		if($selectedCategoryId == null)
			$this->selectedCategory = null;
		else
			$this->selectedCategory = SkillCategory::find($selectedCategoryId);
	}
	
	#[On('refresh-parent')]
	public function refreshParent(?int $parentId)
	{
		if(!$parentId)
		{
			//one of the root categories was deleted, so we refresh our root categories
			$this->rootCategories = SkillCategory::root()
			                                     ->get();
		}
	}
	
	#[On('move-category')]
	public function moveCategory(int $categoryId, int $parentId)
	{
		$category = SkillCategory::find($categoryId);
		$oldParentId = $category->parent_id;
		$category->parent_id = $parentId;
		$category->save();
		$this->dispatch('refresh-parent', parentId: $oldParentId);
		$this->dispatch('refresh-parent', parentId: $parentId);
	}
	
	public function render()
	{
		if($this->search && strlen($this->search) > 2)
		{
			$this->knowledgeResults = KnowledgeSkill::search($this->search)
			                                        ->get()
			                                        ->sortBy('designation');
			$this->characterResults = CharacterSkill::search($this->search)
			                                        ->get()
			                                        ->sortBy('designation');
		}
		else
		{
			$this->knowledgeResults = null;
			$this->characterResults = null;
		}
		return view('livewire.assessment.skill-category-browser');
	}
}
