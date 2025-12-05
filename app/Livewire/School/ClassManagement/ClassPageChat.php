<?php

namespace App\Livewire\School\ClassManagement;

use App\Enums\ClassViewer;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use Livewire\Component;

class ClassPageChat extends Component
{
	public ClassSession $classSession;
	public Person $self;
	public ?StudentRecord $student = null;

	public function mount(ClassSession $session)
	{
		$this->self = auth()->user();
		$this->classSession = $session;
		if($this->classSession->viewingAs(ClassViewer::STUDENT))
		{
			$this->student = $this->self->student();
		}
		elseif($this->classSession->viewingAs(ClassViewer::PARENT))
		{
			$this->student = $this->session->students()
				->whereIn('person_id', $this->self->currentChildStudents()->pluck('person_id')->toArray())
				->first();
		}
	}

    public function render()
    {
        return view('livewire.school.class-management.class-page-chat');
    }
}
