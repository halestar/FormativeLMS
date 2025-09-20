<?php

namespace App\Livewire\Assessment;

use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryItem extends Component
{
	public SkillCategory $category;
	public bool $editing = false;
	public string $name;
	public bool $open = false;
	public bool $selected = false;
	public ?SkillCategory $editingCategory = null;
	public ?SkillCategory $selectedCategory = null;
	public int $depth;
	public ?Collection $children = null;
	public string $classes = '';
	public array $openTo = [];
	
	public function mount(SkillCategory $category, int $depth, string $classes = '', array $openTo = [])
	{
		$this->category = $category;
		$this->depth = $depth;
		$this->name = $category->name;
		$this->children = null;
		$this->classes = $classes;
		$this->openTo = $openTo;
		if(count($this->openTo) > 0)
		{
			if(isset($this->openTo[$this->category->id]))
			{
				$this->open = $this->openTo[$this->category->id]['open'];
				$this->selected = $this->openTo[$this->category->id]['selected'];
				if($this->open)
					$this->children = $this->category->subCategories;
			}
		}
	}
	
	public function toggleCategory()
	{
		$this->open = !$this->open;
		if($this->open && !$this->children)
			$this->children = $this->category->subCategories;
		else
			$this->children = null;
		$this->saveState();
	}
	
	private function saveState()
	{
		$self = auth()->user();
		$state = $self->prefs->get('skill-category-browser-open-to', []);
		if(!$this->open && !$this->selected && isset($state[$this->category->id]))
			unset($state[$this->category->id]);
		else
			$state[$this->category->id] = ['open' => $this->open, 'selected' => $this->selected];
		$self->prefs->set('skill-category-browser-open-to', $state);
		$self->save();
	}
	
	#[On('edit-category')]
	public function setEditCategory(?int $editCategoryId)
	{
		if($editCategoryId == null)
			$this->editing = false;
		else
			$this->editing = ($editCategoryId == $this->category->id);
	}
	
	public function updateName()
	{
		$this->category->name = $this->name;
		$this->category->save();
		$this->dispatch('edit-category', editCategoryId: null)
		     ->to(SkillCategoryBrowser::class);
	}
	
	public function selectCategory()
	{
		$this->selected = !$this->selected;
		$this->dispatch('select-category', selectedCategoryId: $this->selected ? $this->category->id : null);
	}
	
	#[On('select-category')]
	public function receiveSelectCategory(?int $selectedCategoryId)
	{
		if($selectedCategoryId != $this->category->id)
			$this->selected = false;
	}
	
	#[On('new-category')]
	public function updateCategories(int $parentId)
	{
		if($parentId == $this->category->id)
		{
			//a new subcategory was added to this category
			$this->children = $this->category->subCategories;
			$this->open = true;
		}
	}
	
	public function removeCategory()
	{
		if($this->category->canDelete())
		{
			//since we know we don't have anything to worry about, we can safely delete the category
			$this->category->delete();
			//tell the parent to refresh
			$this->dispatch('refresh-parent', parentId: $this->category->parent_id);
			if($this->selected)
			{
				//since we're selected, unselect us.
				$this->dispatch('select-category', selectedCategoryId: null)
				     ->to(SkillCategoryBrowser::class);
			}
		}
	}
	
	#[On('refresh-parent')]
	public function refreshParent(?int $parentId)
	{
		if($parentId && $this->category->id == $parentId)
		{
			//refresh children
			$this->children = $this->category->subCategories;
		}
	}
	
	public function render()
	{
		return view('livewire.assessment.category-item');
	}
}
