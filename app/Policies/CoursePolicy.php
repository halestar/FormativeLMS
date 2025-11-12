<?php

namespace App\Policies;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\SubjectMatter\Course;

class CoursePolicy
{
	/**
	 * Determine whether the user can view any models.
	 */
	public function viewAny(Person $person): bool
	{
		return ($person->can('subjects.courses') &&
			$person->isEmployee());
	}
	
	/**
	 * Determine whether the user can view the model.
	 */
	public function view(Person $person, Course $course): bool
	{
		return ($person->can('subjects.courses') &&
			$person->isEmployee() &&
			$person->employeeCampuses()
			       ->where('campus_id', $course->campus->id)
			       ->exists());
	}
	
	/**
	 * Determine whether the user can create models.
	 */
	public function create(Person $person, Campus $campus): bool
	{
		return ($person->can('subjects.courses') &&
			$person->isEmployee() &&
			$person->employeeCampuses()
			       ->where('campus_id', $campus->id)
			       ->exists());
	}
	
	/**
	 * Determine whether the user can update the model.
	 */
	public function update(Person $person, Course $course): bool
	{
		return ($person->can('subjects.courses') &&
			$person->isEmployee() &&
			$person->employeeCampuses()
			       ->where('campus_id', $course->campus->id)
			       ->exists());
	}
	
	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(Person $person, Course $course): bool
	{
		return ($person->can('subjects.courses') &&
			$person->isEmployee() &&
			$person->employeeCampuses()
			       ->where('campus_id', $course->campus->id)
			       ->exists() &&
			$course->canDelete());
	}
}
