<?php

namespace App\Classes\Settings;


use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CommunicationSettings extends SystemSetting
{
	protected static string $settingKey = "communications";
	
	protected static function defaultValue(): array
	{
		return
			[
				"email_connection_id" => null,
                "email_from" => config('mail.from.name'),
                "email_from_address" => config('mail.from.address'),
                "send_sms" => false,
				"sms_connection_id" => null,
			];
	}

    public function sendSms(): Attribute
    {
        return $this->basicProperty('send_sms');
    }

    public function emailConnectionId(): Attribute
    {
        return $this->basicProperty('email_connection_id');
    }

    public function emailConnection(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes)
            {
                $connectionId = $this->getValue($attributes['value'], 'email_connection_id', null);
                if(!$connectionId) return null;
                return IntegrationConnection::find($connectionId);
            },
        );
    }

	public function smsConnectionId(): Attribute
	{
		return $this->basicProperty('sms_connection_id');
	}

	public function smsConnection(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				$connectionId = $this->getValue($attributes['value'], 'sms_connection_id', null);
				if(!$connectionId) return null;
                return IntegrationConnection::find($connectionId);
			}
		);
	}

    public function emailFrom(): Attribute
    {
        return $this->basicProperty('email_from');
    }

    public function emailFromAddress(): Attribute
    {
        return $this->basicProperty('email_from_address');
    }

    public function availableEmailConnections(): array
    {
        $integrationManager = app(IntegrationsManager::class);
        $emailServices = $integrationManager->getAvailableServices(IntegratorServiceTypes::EMAIL);
        $connections = [];
        foreach($emailServices as $service)
        {
            if($service->canConnectToSystem())
                $connections[] = $service->connectToSystem();
        }
        return $connections;
    }

    public function availableSmsConnections(): array
    {
        $integrationManager = app(IntegrationsManager::class);
        $smsServices = $integrationManager->getAvailableServices(IntegratorServiceTypes::SMS);
        $connections = [];
        foreach($smsServices as $service)
        {
            if($service->canConnectToSystem())
                $connections[] = $service->connectToSystem();
        }
        return $connections;
    }

    public function canSendSms(): bool
    {
        return count($this->availableSmsConnections()) > 0;
    }

    protected function casts(): array
    {
        return [];
    }

}
