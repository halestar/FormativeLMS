<?php

namespace App\View\Components\People;

use App\Casts\IdCard;
use App\Classes\Settings\IdSettings;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IdViewer extends Component
{
    public Person $person;
    public ?IdCard $idCard;
	public IdSettings $idSettings;
    public string $idWidth = "600";
    /**
     * Create a new component instance.
     */
    public function __construct(IdSettings $idSettings, Person $person = null, IdCard $idCard = null, string $size = "md")
    {
		$this->idSettings = $idSettings;
        $this->person = $person?? auth()->user();
        if($idCard)
            $this->idCard = $idCard;
        else
        {
            $this->idCard = null;
            //determine the card.
            if($this->idSettings->idStrategy == IdSettings::ID_STRATEGY_GLOBAL)
                $this->idCard = $this->idSettings->getGlobalId();
            elseif($this->idSettings->idStrategy == IdSettings::ID_STRATEGY_ROLES)
            {
                if($this->person->isStudent())
                    $this->idCard = $this->idSettings->getRoleId(SchoolRoles::StudentRole());
                elseif($this->person->isEmployee())
                    $this->idCard = $this->idSettings->getRoleId(SchoolRoles::EmployeeRole());
                elseif($this->person->isParent())
                    $this->idCard = $this->idSettings->getRoleId(SchoolRoles::ParentRole());
            }
            elseif($this->idSettings->idStrategy == IdSettings::ID_STRATEGY_CAMPUSES)
            {
				$campus = null;
                if($this->person->isStudent())
                    $campus = $this->person->student()->campus;
                elseif($this->person->isEmployee())
                {
                    $campus = $this->person->employeeCampuses()->first();
                }
                elseif($this->person->isParent())
                {
                    $student = $this->person->currentChildStudents()->first();
                    if($student)
                        $campus = $student->campus;
                }
                if($campus)
                    $this->idCard = $this->idSettings->getCampusId($campus);
            }
            else
            {
				$campus = null;
				$role = null;
                if($this->person->isStudent())
                {
                    $role = SchoolRoles::StudentRole();
                    $campus = $this->person->student()->campus;
                }
                elseif($this->person->isEmployee())
                {
                    $role = SchoolRoles::EmployeeRole();
                    $campus = $this->person->employeeCampuses()->first();
                }
                elseif($this->person->isParent())
                {
                    $role = SchoolRoles::ParentRole();
                    $student = $this->person->currentChildStudents()->first();
                    if($student)
                        $campus = $student->campus;
                }
                if($campus && $role)
                    $this->idCard = $this->idSettings->getRoleCampusId($role, $campus);
            }
        }
        $idSizes = config('lms.id_sizes', []);
        if(!isset($idSizes[$size]))
            $size = "md";
        $this->idWidth = $idSizes[$size];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.people.id-viewer');
    }
}
