<?php

namespace App\Livewire\School\ClassManagement;

use App\Enums\ClassViewer;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Support\Collection;
use Livewire\Component;

class LearningDemonstrations extends Component
{
	public ClassSession $classSession;
	public Person $self;
	public bool $canManage = false;
	public string $classes = '';
	public string $style = '';
	public ?Collection $demonstrations = null;
	public ?Collection $opportunities = null;

	public function mount(ClassSession $session)
	{
		$this->self = auth()->user();
		$this->classSession = $session;
		$this->canManage = $this->classSession->viewingAs( ClassViewer::ADMIN) || $this->classSession->viewingAs( ClassViewer::FACULTY);
		//in this case, we get the demonstrations for the class.
		if($this->classSession->viewingAs(ClassViewer::FACULTY))
			$this->demonstrations = $this->classSession->demonstrations;
		elseif($this->classSession->viewingAs(ClassViewer::STUDENT))
			$this->opportunities = $this->self->student()->classOpportunities($this->classSession)->get();
	}

    public function render()
    {
        return view('livewire.school.class-management.learning-demonstrations');
    }
}
