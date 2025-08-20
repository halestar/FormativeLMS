<?php

namespace Database\Seeders;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Document\LocalDocumentStorage;
use App\Classes\Storage\Work\LocalWorkStorage;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$storageSettings = app()->make(StorageSettings::class);
		//student documents
		$storageSettings->employee_documents = [new LocalDocumentStorage('employees', 'My Documents')];
		//employee documents
		$storageSettings->student_documents = [new LocalDocumentStorage('students', 'My Documents')];
		//student work
		$storageSettings->student_work = new LocalWorkStorage('students_inc', 'incoming');
		//student work
		$storageSettings->employee_work = new LocalWorkStorage('employee_inc', 'incoming');
		//student work
		$storageSettings->class_work = new LocalWorkStorage('class_inc', 'incoming');
		//student work
		$storageSettings->email_work = new LocalWorkStorage('emails_inc', 'incoming');
		$storageSettings->save();
	}
}
