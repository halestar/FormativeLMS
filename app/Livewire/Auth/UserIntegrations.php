<?php

namespace App\Livewire\Auth;

use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use App\Models\People\Person;
use Livewire\Component;

class UserIntegrations extends Component
{
	public Person $person;
	public array $integrators = [];
	
	public function mount(?Person $person = null)
	{
		$this->person = $person->id ? $person : auth()->user();
		$this->refreshIntegrators();
	}
	
	public function refreshIntegrators()
	{
		$this->integrators = [];
		foreach(Integrator::all() as $integrator)
		{
			if($integrator->ableToIntegrate($this->person))
				$this->integrators[] = $integrator;
		}
	}
	
	public function removeIntegration(Integrator $integrator)
	{
		$integrator->removeIntegration($this->person);
		$this->refreshIntegrators();
	}
	
	public function disableService(IntegrationService $service)
	{
		$connection = $service->connect($this->person);
		if($connection)
		{
			$connection->enabled = false;
			$connection->save();
		}
		$this->refreshIntegrators();
	}
	
	public function enableService(IntegrationService $service)
	{
		//more complicated than disabling it. First, is there a connection?
		$connection = $service->connect($this->person);
		if(!$connection)
			$connection = $this->person->getServiceConnection($service);
		if($connection)
		{
			$connection->enabled = true;
			$connection->save();
		}
		$this->refreshIntegrators();
	}
	
	public function render()
	{
		return view('livewire.auth.user-integrations');
	}
}
