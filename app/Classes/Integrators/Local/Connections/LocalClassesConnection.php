<?php

namespace App\Classes\Integrators\Local\Connections;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Enums\ClassViewer;
use App\Models\Integrations\Connections\ClassesConnection;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Gate;

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

}