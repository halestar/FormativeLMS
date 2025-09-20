<?php

namespace App\View\Components\Assessment;

use App\Casts\Rubric;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RubricViewer extends Component
{
	/**
	 * Create a new component instance.
	 */
	public function __construct(public Rubric $rubric) {}
	
	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.assessment.rubric-viewer');
	}
}
