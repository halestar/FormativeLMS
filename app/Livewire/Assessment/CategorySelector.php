<?php

namespace App\Livewire\Assessment;

use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Support\Collection;
use Livewire\Component;

class CategorySelector extends Component
{
    public Collection $rootCategories;
    public int $selectedRootCategoryId;
    public array $childrenCategories = [];
    public Collection $latestCategories;
    public int $selectedLatestCategoryId;

    public int $selectedCategoryId;
    public array $childrenIds = [];
    public function mount()
    {
        $this->rootCategories = SkillCategory::root()->get();
        $this->selectedRootCategoryId = $this->rootCategories->first()->id;
        $this->childrenCategories = [];
        $this->latestCategories = $this->rootCategories->first()->subCategories;
        $this->selectedLatestCategoryId = 0;
        $this->selectedCategoryId = $this->selectedRootCategoryId;
        $this->childrenIds = [];
    }

    public function updatePos($childPos, $childId)
    {
        $this->childrenCategories[$childPos] = $childId;
    }
    public function selectRootCategory()
    {
        $this->childrenCategories = [];
        $this->latestCategories = $this->rootCategories->where('id', $this->selectedRootCategoryId)->first()->subCategories;
        $this->selectedLatestCategoryId = 0;
        $this->selectedCategoryId = $this->selectedRootCategoryId;
    }

    public function selectLatestCategory()
    {
        if($this->selectedLatestCategoryId != 0)
        {
            $this->selectedCategoryId = $this->selectedLatestCategoryId;
            $children = $this->latestCategories->where('id', $this->selectedLatestCategoryId)->first()->subCategories;
            if($children->count() > 0)
            {
                $this->childrenCategories[] = $this->latestCategories;
                $this->childrenIds[] = $this->selectedLatestCategoryId;
                $this->latestCategories = $children;
                $this->selectedLatestCategoryId = 0;
            }
        }
        else
        {
            if(count($this->childrenCategories) == 0)
                $this->selectedCategoryId = $this->selectedLatestCategoryId;
            else
            {
                //we need to figure out the one before this one. is it in children?
                if(count($this->childrenCategories) > 0)
                {
                    $this->selectedCategoryId = $this->childrenIds[count($this->childrenCategories) - 1];
                }
            }
        }
    }

    public function selectChildCategory(int $childPos, int $childId)
    {
        $this->selectedCategoryId = $childId;
        $this->childrenCategories = array_slice($this->childrenCategories, 0, $childPos);
        $this->childrenIds = array_slice($this->childrenIds, 0, $childPos);
        $this->childrenIds[($childPos - 1)] = $childId;
        $this->latestCategories = $this->childrenCategories[($childPos - 1)]->where('id', $childId)->first()->subCategories;
        $this->selectedLatestCategoryId = 0;
    }

    public function render()
    {
        return view('livewire.assessment.category-selector');
    }
}
