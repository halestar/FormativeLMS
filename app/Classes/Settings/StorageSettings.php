<?php

namespace App\Classes\Settings;

use App\Casts\Utilities\SystemSettings\AsDocumentStorage;
use App\Casts\Utilities\SystemSettings\AsWorkStorage;
use App\Classes\Storage\Document\LocalDocumentStorage;
use App\Classes\Storage\LmsStorage;
use App\Classes\Storage\Work\LocalWorkStorage;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StorageSettings extends SystemSetting
{
    protected function casts(): array
    {
        return
            [
                'employee_documents' => AsDocumentStorage::class,
                'student_documents' => AsDocumentStorage::class,
                'student_work' => AsWorkStorage::class,
                'employee_work' => AsWorkStorage::class,
                'class_work' => AsWorkStorage::class,
                'email_work' => AsWorkStorage::class,
            ];
    }
    protected static string $settingKey = "storage";

    protected static function defaultValue(): array
    {
        return
            [
                "document_storages" =>
                    [
                        LocalDocumentStorage::class,
                    ],
                "work_storages" =>
                    [
                        LocalWorkStorage::class,
                    ],
                'employee_documents' => [],
                'student_documents' => [],
                'student_work' => null,
                'employee_work' => null,
                'class_work' => null,
                'email_work' => null,
            ];
    }

    public function documentStorages(): Attribute
    {
        return $this->basicProperty('document_storages');
    }

    public function workStorages(): Attribute
    {
        return $this->basicProperty('work_storages');
    }

    public function instances(string $except = null): array
    {
        $instances = [];
        if($this->student_work && !($except && $this->student_work->instanceProperty == $except))
            $instances[] = $this->student_work->instanceProperty;
        if($this->employee_work && !($except && $this->employee_work->instanceProperty == $except))
            $instances[] = $this->employee_work->instanceProperty;
        if($this->class_work && !($except && $this->class_work->instanceProperty == $except))
            $instances[] = $this->class_work->instanceProperty;
	    if($this->email_work && !($except && $this->email_work->instanceProperty == $except))
		    $instances[] = $this->email_work->instanceProperty;
        foreach($this->employee_documents as $doc)
        {
			if($except && $doc->instanceProperty == $except)
				continue;
            $instances[] = $doc->instanceProperty;
        }
        foreach($this->student_documents as $doc)
        {
	        if($except && $doc->instanceProperty == $except)
		        continue;
	        $instances[] = $doc->instanceProperty;
        }

        return $instances;
    }
	
	public function getInstance(string $instance): ?LmsStorage
	{
		if($this->student_work && $this->student_work->instanceProperty == $instance)
			return $this->student_work->instanceProperty;
		if($this->employee_work && $this->employee_work->instanceProperty == $instance)
			return $this->employee_work->instanceProperty;
		if($this->class_work && $this->class_work->instanceProperty == $instance)
			return $this->class_work->instanceProperty;
		if($this->email_work && $this->email_work->instanceProperty == $instance)
			return $this->email_work->instanceProperty;
		foreach($this->employee_documents as $doc)
			if($doc->instanceProperty == $instance)
				return $doc;
		foreach($this->student_documents as $doc)
			if($doc->instanceProperty == $instance)
				return $doc;
		return null;
	}
}