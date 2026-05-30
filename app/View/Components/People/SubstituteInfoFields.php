<?php

namespace App\View\Components\People;

use App\Models\People\Person;
use App\Models\Substitutes\Substitute;
use App\Models\Utilities\SchoolRoles;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubstituteInfoFields extends Component
{
    public ?Substitute $substitute;

    public bool $active;

    /**
     * Create a new component instance.
     */
    public function __construct(public Person $person)
    {
        $this->substitute = $person->substituteProfile;
        $this->active = $person->hasRole(SchoolRoles::$SUBSTITUTE);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.people.substitute-info-fields');
    }
}
