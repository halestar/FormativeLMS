<?php

namespace App\View\Components\Homepage;

use App\Models\People\Person;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class UpcomingSubRequests extends Component
{
	public Collection $subRequests;

	/**
	 * Create a new component instance.
	 */
	public function __construct(public Person $faculty)
	{
		$this->subRequests = $this->faculty->substituteRequests()->upcoming()->get();
	}

	public function shouldRender(): bool
	{
		return $this->faculty->substituteRequests()->upcoming()->count() > 0;
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		return view('components.homepage.upcoming-sub-requests');
	}
}
