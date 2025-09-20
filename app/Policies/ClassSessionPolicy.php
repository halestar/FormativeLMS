<?php

namespace App\Policies;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;

class ClassSessionPolicy
{
	/**
	 * Determine whether the user can view any models.
	 */
	public function viewAny(Person $person): bool
	{
		return true;
	}
	
	/**
	 * Determine whether the user can view the model.
	 */
	public function view(Person $person, ClassSession $classSession): bool
	{
		//if you have the classes.view
		if($person->can('subjects.classes.view'))
			return true;
		//you can see this class if you'\re a teacher for this class.
		if($classSession->teachers()
		                ->where('person_id', $person->id)
		                ->exists())
			return true;
		//or if you're a student enrolled in the class.
		if($classSession->students()
		                ->where('person_id', $person->id)
		                ->exists())
			return true;
		//else if it's a parent and on of their kids can see the class.
		if($person->isParent())
			return $classSession
				->students()
				->whereIn('person_id', $person->currentChildStudents()
				                              ->pluck('person_id')
				                              ->toArray())
				->exists();
		return false;
	}
	
	/**
	 *  Determine if user can manage the model.
	 */
	public function manage(Person $person, ClassSession $classSession): bool
	{
		if($person->can('subjects.classes.manage'))
			return true;
		//else, only the teacher for the class can do it.
		return $classSession->teachers()
		                    ->where('person_id', $person->id)
		                    ->exists();
	}
}
