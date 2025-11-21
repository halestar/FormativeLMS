<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Enums\ClassViewer;
use App\Models\Integrations\Connections\ClassesConnection;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;

class LocalClassesConnection extends ClassesConnection
{
	public function manageClass(Person $person, ClassSession $classSession, ClassViewer $viewRole): mixed
	{
		return view('integrators.local.classes', compact('person', 'classSession', 'viewRole'));
	}

	/**
	 * @inheritDoc
	 */
	public static function getSystemInstanceDefault(): array
	{
		return [];
	}

}