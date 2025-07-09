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
    public string $idWidth = "600";
    /**
     * Create a new component instance.
     */
    public function __construct(Person $person = null, IdCard $idCard = null, string $size = "md")
    {
        $this->person = $person?? auth()->user();
        if($idCard)
            $this->idCard = $idCard;
        else
        {
            $this->idCard = null;
            //determine the card.
            $idSettings = IdSettings::instance();
            if($idSettings->idStrategy == IdSettings::ID_STRATEGY_GLOBAL)
                $this->idCard = $idSettings->getGlobalId();
            elseif($idSettings->idStrategy == IdSettings::ID_STRATEGY_ROLES)
            {
                if($this->person->isStudent())
                    $this->idCard = $idSettings->getRoleId(SchoolRoles::StudentRole());
                elseif($this->person->isEmployee())
                    $this->idCard = $idSettings->getRoleId(SchoolRoles::EmployeeRole());
                elseif($this->person->isParent())
                    $this->idCard = $idSettings->getRoleId(SchoolRoles::ParentRole());
            }
            elseif($idSettings->idStrategy == IdSettings::ID_STRATEGY_CAMPUSES)
            {
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
                    $this->idCard = $idSettings->getCampusId($campus);
            }
            else
            {
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
                    $this->idCard = $idSettings->getRoleCampusId($role, $campus);
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
