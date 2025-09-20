<?php

namespace App\View\Components\Homepage;

use App\Models\People\Person;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class FacultyClasses extends Component
{
	public Collection $classes;
	
	/**
	 * Create a new component instance.
	 */
	public function __construct(public Person $faculty)
	{
		$this->classes = $faculty->currentClassSessions;
	}
	
	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.homepage.faculty-classes');
	}
}
