<?php

namespace App\Livewire\School;

use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ClassManagementSettings extends Component
{
	public string $classes = '';
	public string $style = '';
	public Person $self;
	public SchoolClass $schoolClass;
	public array $connections = [];
	public array $sessionConnections;

	public function mount(SchoolClass $schoolClass, IntegrationsManager $manager)
	{
		$this->authorize('manage', $schoolClass);
		$this->self = auth()->user();
		$this->schoolClass = $schoolClass;

		$this->sessionConnections = $this->schoolClass
			->sessions
			->mapWithKeys(fn(ClassSession $session) => [$session->id => $session->class_management_id])
			->toArray();
		//we need to figure out the possible connections for the class manager
		$classServices = $manager->getAvailableServices(IntegratorServiceTypes::CLASSES);
		foreach($classServices as $service)
		{
			//we always take the system connection first.
			$conn = $service->connectToSystem();
			//if we can't connect to the system, connect to the person.
			if(!$conn)
				$conn = $service->connect($this->self);
			if($conn)
				$this->connections[] = $conn;
		}
	}

	public function apply(int $classSessionId)
	{
		if(isset($this->sessionConnections[$classSessionId]))
		{
			$session = $this->schoolClass->sessions->find($classSessionId);
			if($session)
			{
				$session->class_management_id = $this->sessionConnections[$classSessionId];
				$session->save();
			}
		}
	}

	public function applyToAll(int $classSessionId)
	{
		if(isset($this->sessionConnections[$classSessionId]))
		{
			foreach($this->schoolClass->sessions as $session)
			{
				$session->class_management_id = $this->sessionConnections[$classSessionId];
				$session->save();
			}
		}
	}


    public function render()
    {
        return view('livewire.school.class-management-settings');
    }
}
