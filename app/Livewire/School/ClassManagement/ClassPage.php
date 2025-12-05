<?php

namespace App\Livewire\School\ClassManagement;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Classes\Integrators\Local\ClassManagement\ClassTabs;
use App\Classes\Integrators\Local\ClassManagement\ClassTab;
use App\Enums\ClassViewer;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassStatus;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

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
		$this->canManage = $this->classSession->viewingAs( ClassViewer::ADMIN) || $this->classSession->viewingAs( ClassViewer::FACULTY);
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
			];
	}
	
	public function render()
	{
		return view('livewire.school.class-management.class-page');
	}
}
