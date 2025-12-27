<?php

namespace App\Livewire\School\ClassManagement;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\ClassViewer;
use App\Enums\WorkStoragesInstances;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassStatus;
use App\Models\Utilities\MimeType;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class ClassPage extends Component
{
    public Person $self;
	public ClassSession $classSession;
    public ClassSessionLayoutManager $layout;
    public ClassStatus $topAnnouncement;
    public bool $canManage;
	
	public bool $editing = false;

	public function mount(ClassSession $classSession)
	{
        $this->self = auth()->user();
		$this->classSession = $classSession;
		$this->layout = $this->classSession->classManager->getClassLayoutManager($this->classSession);
		$this->canManage = $this->classSession->viewingAs( ClassViewer::FACULTY);
	}
	
	public function setEdit(bool $editing)
	{
		if($this->canManage)
			$this->editing = $editing;
	}

	public function getListeners()
	{
		return
			[
				"class-page-set-editing" => 'setEdit',
				'document-storage-browser-files-selected' => 'updateImage',
			];
	}

	public function updateImage($cb_instance, $selected_items)
	{
		if($cb_instance == 'class-img')
		{
			$doc = DocumentFile::hydrate($selected_items[0]);
			$this->layout->updateClassImageFile($doc);
		}

	}
	
	public function render()
	{
		return view('livewire.school.class-management.class-page');
	}
}
