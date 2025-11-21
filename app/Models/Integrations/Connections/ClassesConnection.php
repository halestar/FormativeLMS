<?php

namespace App\Models\Integrations\Connections;

use App\Enums\ClassViewer;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;

abstract class ClassesConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
	final public static function getInstanceDefault(): array
	{
		return [];
	}
	abstract public function manageClass(Person $person, ClassSession $classSession, ClassViewer $viewRole): mixed;
}