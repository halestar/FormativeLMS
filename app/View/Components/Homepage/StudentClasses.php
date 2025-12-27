<?php

namespace App\View\Components\Homepage;

use App\Models\People\StudentRecord;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class StudentClasses extends Component
{
	public Collection $classes;
	
	/**
	 * Create a new component instance.
	 */
	public function __construct()
	{
		$self = auth()->user();
		$student = null;
		if($self->isStudent())
			$student = $self->student();
		elseif($self->isParent())
			$student = $self->viewingStudent;
		$this->classes = new Collection();
		if($student)
			$this->classes = $student->classSessions;
	}
	
	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.homepage.student-classes');
	}
}
