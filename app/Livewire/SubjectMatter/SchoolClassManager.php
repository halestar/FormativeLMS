<?php

namespace App\Livewire\SubjectMatter;

use App\Classes\Days;
use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Component;

class SchoolClassManager extends Component
{
    public SchoolClass $schoolClass;
    public Campus $campus;
    public Collection $sessions;
    public ?ClassSession $classSession = null;

    //class session parameters
    public string $scheduleType = "block";
    public string $room_type = "single";
    public ?int $room_id = null;
    public ?int $block_id = null;
    public array $periods = [];
    public Collection $teachers;

    public string $search_teacher = "";
    public array $multiple_rooms = [];

    public function mount(SchoolClass $schoolClass)
    {
        $this->schoolClass = $schoolClass;
        $this->sessions = $this->schoolClass->sessions;
        $this->campus = $schoolClass->course->campus;
        $this->loadSession($this->sessions->first());
    }

    public function createClassSession(Term $term)
    {
        $classSession = new ClassSession();
        $classSession->class_id = $this->schoolClass->id;
        $classSession->term_id = $term->id;
        $classSession->save();
        $this->sessions = $this->schoolClass->sessions;
        $this->loadSession($classSession);
    }

    public function loadSession(?ClassSession $classSession)
    {

        $this->classSession = $classSession;
        if($this->classSession)
        {
            foreach (Days::getWeekdays() as $day)
                $this->periods[$day] = [];
            $this->room_id = $this->classSession->room_id;
            if ($classSession->block_id)
            {
                $this->scheduleType = "block";
                $this->block_id = $classSession->block_id;
            }
            else
            {
                $this->scheduleType = "periods";
                if($this->room_id)
                    $this->room_type = "single";
                else
                {
                    $this->room_type = "multiple";
                    $this->multiple_rooms = [];
                    foreach($this->classSession->periods as $period)
                        $this->multiple_rooms[$period->id] = $period->sessionPeriod->room_id;
                }
                foreach (Days::getWeekdays() as $day)
                    $this->periods[$day] = $this->classSession->periods()->where('day', $day)->pluck('id')->toArray();
            }
            $this->teachers = $classSession->teachers;
        }

    }

    public function setTerm(Term $term = null)
    {
        $this->loadSession($this->schoolClass->termSession($term));
    }

    public function suggestTeachers()
    {
        return $this->campus->employeesByRole(SchoolRoles::$FACULTY)->filter(function(Person $teacher)
        {
            return str_contains(strtolower($teacher->first), strtolower($this->search_teacher)) ||
                str_contains(strtolower($teacher->last), strtolower($this->search_teacher)) ||
                str_contains(strtolower($teacher->nick), strtolower($this->search_teacher)) ||
                str_contains(strtolower($teacher->email), strtolower($this->search_teacher));
        });
    }

    public function addTeacher(Person $teacher)
    {
        $this->teachers->push($teacher);
        $this->search_teacher = "";
    }

    public function removeTeacher(Person $teacher)
    {
        $this->teachers->forget($this->teachers->search($teacher));
    }

    private function saveData(ClassSession $classSession)
    {
        $classSession->room_id = $this->room_id;
        if($this->scheduleType == "block")
        {
            $classSession->block()->associate($this->block_id);
            $classSession->periods()->detach();
        }
        else
        {
            $classSession->block()->dissociate();
            if($this->room_type == "multiple")
            {
                $classSession->room_id = null;
                $periods = [];
                foreach($this->multiple_rooms as $period_id => $room_id)
                    $periods[$period_id] = ['room_id' => $room_id];
                $classSession->periods()->sync($periods);
            }
            else
            {
                $periods = [];
                foreach($this->periods as $day)
                    $periods = array_merge($periods, $day);
                foreach($periods as $period_id)
                    $periods[$period_id] = ['room_id' => null];
                $classSession->periods()->sync($periods);
            }
        }
        $classSession->teachers()->sync($this->teachers->pluck('id'));
        $classSession->save();
    }

    public function save()
    {
        if($this->classSession)
        {
            $this->saveData($this->classSession);
        }
        else
        {
            foreach($this->sessions as $session)
                $this->saveData($session);
        }
        return redirect()->route('subjects.classes.index', $this->schoolClass->course->id);
    }
    public function render()
    {
        $suggestedTeachers = null;
        if(strlen($this->search_teacher) > 2)
            $suggestedTeachers = $this->suggestTeachers();
        return view('livewire.subject-matter.school-class-manager', ['suggestedTeachers' => $suggestedTeachers]);
    }
}
