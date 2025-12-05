<?php

namespace App\Classes\Auth;

use App\Classes\Integrators\IntegrationsManager;
use App\Interfaces\Synthesizable;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AuthenticationDesignation implements Synthesizable
{
	public const DEFAULT_PRIORITY = 0;
	public IntegrationService|Collection|null $services;
	
	public function __construct
	(
        // The priority of this designation
		public int $priority,
        // The roles that this designation applies to
        public array $roles,
        // Either a single IntegratorService id or, if it's the user's choice, the available choices that the user can
        // select from, or if null, to block authentication.
		private array|int|null $service_ids
	)
	{
		if(is_array($service_ids))
			$this->services = IntegrationService::whereIn('id', $this->service_ids)
			                                    ->get();
		elseif($service_ids != null)
			$this->services = IntegrationService::find($this->service_ids);
        else
            $this->services = null;
	}

    public static function hydrate(array $data): static
	{
		$priority = $data['priority'];
		$roles = $data['roles'];
		$service_ids = $data['service_ids']?? null;
		$auth = new AuthenticationDesignation($priority, $roles, $service_ids);
		return $auth;
	}
	
	public static function makeDefaultDesignation($priority = self::DEFAULT_PRIORITY): AuthenticationDesignation
	{
		$manager = App::make(IntegrationsManager::class);
		$roles = [];
		$service_ids = null;
		return new AuthenticationDesignation($priority, $roles, $service_ids);
	}
	
	public function updateServices(int|array|null $service_ids)
	{
		$this->service_ids = $service_ids;
		if(is_array($service_ids))
        {
            $this->services = IntegrationService::whereIn('id', $this->service_ids)
                ->get();
        }
		elseif($service_ids != null)
        {
            $this->services = IntegrationService::find($this->service_ids);
            Log::debug('in not null, services now: '  . print_r($this->services, true));
        }
        else
        {
            $this->services = null;
            Log::debug('in null, services now null');
        }
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray(): array
	{
		return
			[
				'priority' => $this->priority,
				'roles' => $this->roles,
				'service_ids' => $this->service_ids,
			];
	}
	
	public function appliesToPerson(Person $person)
	{
		return ($this->priority === self::DEFAULT_PRIORITY) || ($person->hasAnyRole($this->roles));
	}
	
	public function prettyServices(): string
	{
        Log::debug('in priority ' . $this->priority . ' services: ' . ($this->services instanceof Collection ? 'collection': ($this->services instanceof IntegrationService ? 'object': 'null')));
		if($this->services instanceof Collection)
			return $this->services->pluck('name')
			                      ->implode(', ');
        if($this->services instanceof IntegrationService)
    		return $this->services->name;
        return __('auth.block');
	}
	
	public function isChoice(): bool
	{
		return ($this->services instanceof Collection);
	}
}