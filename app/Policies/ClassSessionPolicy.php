<?php

namespace App\Policies;

use App\Enums\ClassViewer;
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
		return ClassViewer::determineType($person, $classSession) !== null;
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
