<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Casts\Learning\Rubric;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class LearningDemonstrationRubricSelector extends Component
{
	public Rubric $originalRubric;
	#[Modelable]
	public ?Rubric $rubric = null;
	
	public function mount(Rubric $rubric)
	{
		$this->originalRubric = $rubric;
		if(!$this->rubric)
			$this->rubric = $rubric;
	}
	
	public function resetRubric()
	{
		$this->rubric = $this->originalRubric;
	}
	
	public function removeCriteria(int $pos)
	{
		if($this->rubric->numCriteria() > 1)
			$this->rubric->removeCriteria($pos);
	}
	
    public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-rubric-selector');
    }
}
