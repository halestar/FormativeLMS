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
        $services = IntegrationService::where('can_connect_to_people', true)->get();
        foreach ($services as $service) {
            if (! $service->canRegister($this->person)) {
                continue;
            }
            if (! isset($this->integrators[$service->integrator_id])) {
                $this->integrators[$service->integrator_id] =
                    [
                        'integrator' => $service->integrator,
                        'services' => [],
                    ];
            }
            $this->integrators[$service->integrator_id]['services'][] =
            [
                'service' => $service,
                'connection' => $service->connect($this->person),
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
        if ($connection) {
            $connection->enabled = false;
            $connection->save();
        }
        $this->refreshIntegrators();
    }

    public function enableService(IntegrationService $service)
    {
        // more complicated than disabling it. First, is there a connection?
        $connection = $service->connect($this->person);
        if (! $connection) {
            $connection = $this->person->getServiceConnection($service);
        }
        if ($connection) {
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
