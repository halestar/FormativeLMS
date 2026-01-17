<?php

namespace App\Livewire\School;

use App\Classes\SessionSettings;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Component;

class ClassCriteriaManager extends Component
{
	public Person $faculty;
	public Collection $sessions;
	public ?SchoolClass $classSelected = null;

	public Collection $classCriteria;
	public Collection $schoolClasses;
	public Collection $importYears;
	public ?int $importYearId = null;
	public Collection $importClasses;
	public ?int $importClassId = null;
	public string $classes = '';
	public string $style = '';
	
	public function mount(SchoolClass $schoolClass = null)
	{
		$this->authorize('has-role', SchoolRoles::$FACULTY);
		$this->faculty = auth()->user();
		$this->sessions = $schoolClass->sessionsTaughtBy($this->faculty)->get();
		$this->schoolClasses = $this->faculty->currentSchoolClasses();

		$classesTaught = $this->faculty->classesTaught()->has('classCriteria')->groupBy('class_sessions.class_id')->get();
		$this->importYears = new Collection();
		$this->importClasses = new Collection();
		foreach($classesTaught as $class)
		{
			if(!isset($this->importYears[$class->term->year_id]))
			{
				$this->importYears[$class->term->year_id] = $class->term->year;
				$this->importClasses[$class->term->year_id] = new Collection();
			}
			$this->importClasses[$class->term->year_id][] = $class->schoolClass;
		}
		if($this->importYears->isNotEmpty())
		{
			$this->importYearId = $this->importYears->first()->id;
			$this->importClassId = $this->importClasses[$this->importYearId]->first()->id;
		}
		$this->classCriteria = $schoolClass->classCriteria;
	}
	
	public function addCriteria()
	{
		$criteria = new ClassCriteria();
		$criteria->class_id = $this->classSelected;
		$criteria->name = __('learning.criteria.name.new');
		$criteria->abbreviation = __('learning.criteria.abbr.new');
		$criteria->save();
		foreach($this->sessions as $session)
			$session->classCriteria()->attach($criteria);
		$this->classCriteria->push($criteria);
	}
	
	public function updateCriteriaName(ClassCriteria $criteria, string $name)
	{
		$criteria->name = $name;
		$criteria->save();
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function updateCriteriaAbbr(ClassCriteria $criteria, string $abbr)
	{
		$criteria->abbreviation = $abbr;
		$criteria->save();
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function updateCriteriaWeight(ClassSession $session, ClassCriteria $criteria, float $default)
	{
		$session->classCriteria()->updateExistingPivot($criteria, ['weight' => $default]);
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function deleteCriteria(ClassCriteria $criteria)
	{
		if($criteria->canDelete())
			$criteria->delete();
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function removeSessionCriteria(ClassSession $session, ClassCriteria $criteria)
	{
		$session->classCriteria()->detach($criteria);
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function attachSessionCriteria(ClassSession $session, ClassCriteria $criteria)
	{
		$session->classCriteria()->attach($criteria);
		$this->classCriteria = $criteria->schoolClass->classCriteria;
	}
	
	public function copyCriteria(ClassSession $fromSession)
	{
		$syncArr = [];
		foreach($fromSession->classCriteria as $criteria)
			$syncArr[$criteria->id] = ['weight' => $criteria->sessionCriteria->weight];
		foreach($this->sessions as $session)
		{
			if($session->id == $fromSession->id)
				continue;
			$session->classCriteria()->sync($syncArr);
		}
		$this->classCriteria = $fromSession->schoolClass->classCriteria;
	}
	
	public function copyToClasses(array $classIds)
	{
		$classCriteria = $this->classCriteria;
		//prepare the weights as well.
		$weights = [];
		$defaults = [];
		foreach($this->sessions as $session)
		{
			$weights[$session->term_id] = [];
			foreach($classCriteria as $criteria)
			{
				$weights[$session->term_id][$criteria->id] = $session->getCriteria($criteria)?->sessionCriteria->weight;
				if(!isset($defaults[$criteria->id]))
					$defaults[$criteria->id] = $weights[$session->term_id][$criteria->id];
			}
		}
		//copy all the criteria for all the classes.
		foreach($classCriteria as $criteria)
		{
			foreach($classIds as $schoolClassId)
			{
				if($schoolClassId == $this->classSelected)
					continue;
				$newCriteria = $criteria->replicate()->fill(['class_id' => $schoolClassId]);
				$newCriteria->save();
				//next, we link all the sessions to this new criteria
				foreach($this->sessions as $session)
				{
					if(isset($weights[$session->term_id][$criteria->id]))
						$session->classCriteria()->attach($newCriteria, ['weight' => $weights[$session->term_id][$criteria->id]]);
					else
						$session->classCriteria()->attach($newCriteria, ['weight' => $defaults[$criteria->id]]);
				}
			}
		}
	}
	
	public function importCriteria(SchoolClass $schoolClass)
	{
		$importCriteria = $schoolClass->classCriteria;
		foreach($importCriteria as $criteria)
		{
			$weight = $schoolClass->sessions->first()->getCriteria($criteria)->sessionCriteria->weight;
			$newCriteria = $criteria->replicate()->fill(['class_id' => $this->classSelected]);
			$newCriteria->save();
			foreach($this->sessions as $session)
				$session->classCriteria()->attach($newCriteria, ['weight' => $weight]);
		}
		$this->classCriteria = $this->schoolClasses->where('id', $this->classSelected)->first()->classCriteria;
	}
	
    public function render()
    {
	    return view('livewire.school.class-criteria-manager');
    }
}
