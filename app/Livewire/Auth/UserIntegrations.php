<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AiSettings;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use App\Models\People\Person;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class UserIntegrations extends Component
{
	public Person $person;

	public array $integrators = [];

	public function mount()
	{
		$aiSettings = app()->make(AiSettings::class);
		$this->person = $this->person ?? auth()->user();
		Log::info(print_r($this->person, true));
		$services = IntegrationService::where('can_connect_to_people', true)->get();
		foreach ($services as $service)
		{
			if (!$service->canRegister($this->person) && !$service->canConfigure($this->person))
				continue;
			if($service->service_type == IntegratorServiceTypes::AI && !$aiSettings->allow_user_ai)
				continue;
			if (!isset($this->integrators[$service->integrator_id]))
			{
				$this->integrators[$service->integrator_id] =
					[
						'name' => $service->integrator->name,
						'services' => [],
					];
			}
			$this->integrators[$service->integrator_id]['services'][$service->id] =
				[
					'name' => $service->name,
					'connection' => $service->connect($this->person),
					'registration_url' => $service->registrationUrl($this->person),
					'configuration_url' => $service->configurationUrl($this->person),
				];
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
		if ($connection)
		{
			$connection->enabled = false;
			$connection->save();
		}
		$this->refreshIntegrators();
	}

	public function enableService(IntegrationService $service)
	{
		// more complicated than disabling it. First, is there a connection?
		$connection = $service->connect($this->person);
		if (!$connection)
		{
			$connection = $this->person->getServiceConnection($service);
		}
		if ($connection)
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
