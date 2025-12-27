<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Models\Integrations\Connections\ClassesConnection;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;

class LocalClassesConnection extends ClassesConnection
{
	public function manageClass(ClassSession $classSession): mixed
	{
        $breadcrumb =
            [
                $classSession->name_with_schedule => '#',
            ];
		return view('integrators.local.classes', compact('classSession', 'breadcrumb'));
	}

    public function getClassLayoutManager(ClassSession $classSession): ClassSessionLayoutManager
    {
        return new ClassSessionLayoutManager($classSession);
    }

	/**
	 * @inheritDoc
	 */
	public static function getSystemInstanceDefault(): array
	{
		return [];
	}

	public static function getInstanceDefault(): array
	{
		return [];
	}

	public function hasPreferences(): bool
	{
		return false;
	}

	public function preferencesRoute(SchoolClass $schoolClass): string
	{
		return '';
	}
}