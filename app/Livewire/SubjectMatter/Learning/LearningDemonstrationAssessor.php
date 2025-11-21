<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use Livewire\Component;

class LearningDemonstrationAssessor extends Component
{
	public array $breadcrumb;
	public Person $faculty;
    public LearningDemonstration $ld;
	public bool $canUseAI = false;

	public function mount(LearningDemonstration $ld, ClassSession $classSession)
	{
		$this->ld = $ld;

		$this->breadcrumb =
			[
				trans_choice('learning.demonstrations', 2) =>
					route('learning.ld.index'),
				__('learning.demonstrations.assess') => '#',
			];
		$this->faculty = auth()->user();
		$this->canUseAI = $this->faculty->canUseAI();
	}


	public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-assessor');
    }
}
