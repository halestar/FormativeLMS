<?php

namespace App\View\Components\People;

use App\Models\People\Person;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BasicInfoFields extends Component
{
    public Person $self;

    /**
     * Create a new component instance.
     */
    public function __construct(public Person $person)
    {
        $this->self = auth()->user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.people.basic-info-fields');
    }
}
