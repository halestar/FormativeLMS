<?php

namespace App\Enums;

use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;

enum ClassViewer
{
    case STUDENT;
	case FACULTY;
	case PARENT;
	case ADMIN;
	public static function determineType(Person $person, ClassSession $session): ?ClassViewer
	{
		//student?
		if($person->isStudent() && $session->students()->where('person_id', $person->id)->exists())
			return self::STUDENT;
		if($person->isTeacher() && $session->teachers()->where('person_id', $person->id)->exists())
			return self::FACULTY;
		if($person->isParent() &&
		   $session->students()->whereIn('person_id', $person->currentChildStudents()->pluck('person_id')->toArray())->exists())
			return self::PARENT;
		if($person->can('subjects.classes.view'))
			return self::ADMIN;
		return null;
	}
}
