<?php

namespace App\Livewire\School\ClassManagement;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Support\Collection;
use Livewire\Component;

class ClassRoster extends Component
{
	public ClassSession $session;
	public Collection $teachers;
	public Collection $students;

	public function mount(ClassSession $session)
	{
		$this->session = $session;
		$this->teachers = $session->teachers;
		$this->students = $session->students;
	}

    public function render()
    {
        return view('livewire.school.class-management.class-roster');
    }
}
