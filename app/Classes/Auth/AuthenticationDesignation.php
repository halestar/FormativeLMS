<?php

namespace App\Classes\Auth;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use JsonSerializable;

class AuthenticationDesignation implements Arrayable, JSONSerializable
{
	public const DEFAULT_PRIORITY = 0;
	public IntegrationService|Collection $services;
	
	public function __construct
	(
		public int $priority, // The priority of this designation
		public array $roles, // The roles that this designation applies to
		private array|int $service_ids // Either a single IntegratorService id or, if it's the user's choice, the available choices that the user can choose from
	)
	{
		if(is_array($service_ids))
			$this->services = IntegrationService::whereIn('id', $this->service_ids)
			                                    ->get();
		else
			$this->services = IntegrationService::find($this->service_ids);
	}
	
	public function updateServices(int|array $service_ids)
	{
		$this->service_ids = $service_ids;
		if(is_array($service_ids))
			$this->services = IntegrationService::whereIn('id', $this->service_ids)
			                                    ->get();
		else
			$this->services = IntegrationService::find($this->service_ids);
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
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public static function hydrate(array $data): AuthenticationDesignation
	{
		$priority = $data['priority'];
		$roles = $data['roles'];
		$service_ids = $data['service_ids'];
		$auth = new AuthenticationDesignation($priority, $roles, $service_ids);
		return $auth;
	}
	
	public static function makeDefaultDesignation($priority = self::DEFAULT_PRIORITY): AuthenticationDesignation
	{
		$manager = App::make(IntegrationsManager::class);
		$roles = [];
		$service_ids = LocalIntegrator::autoload()->services()->ofType(IntegratorServiceTypes::AUTHENTICATION)->first()->id;
		return new AuthenticationDesignation($priority, $roles, $service_ids);
	}
	
	public function appliesToPerson(Person $person)
	{
		return ($this->priority === self::DEFAULT_PRIORITY) || ($person->hasAnyRole($this->roles));
	}
	
	public function prettyServices(): string
	{
		if($this->services instanceof Collection)
			return $this->services->pluck('name')->implode(', ');
		return $this->services->name;
	}
	
	public function isChoice(): bool
	{
		return ($this->services instanceof Collection);
	}
}