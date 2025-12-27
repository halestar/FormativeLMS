<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalClassesConnection;
use App\Enums\IntegratorServiceTypes;
use App\Livewire\School\ClassManagement\ClassAnnouncements;
use App\Livewire\School\ClassManagement\ClassLinks;
use App\Livewire\School\ClassManagement\ClassPageChat;
use App\Livewire\School\ClassManagement\LearningDemonstrations;
use App\Livewire\School\ClassManagement\TopAnnouncement;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;
use App\Models\SubjectMatter\Components\ClassStatus;

class LocalClassesService extends LmsIntegrationService
{
	/**
	 * @inheritDoc
	 */
	public static function getServiceType(): IntegratorServiceTypes
	{
		return IntegratorServiceTypes::CLASSES;
	}

	/**
	 * @inheritDoc
	 */
	public static function getServiceName(): string
	{
		return __('integrators.local.classes');
	}

	/**
	 * @inheritDoc
	 */
	public static function getServiceDescription(): string
	{
		return __('integrators.local.classes.description');
	}

	/**
	 * @inheritDoc
	 */
	public static function getDefaultData(): array
	{
		return
        [
            'available' =>
            [
                ClassAnnouncements::class => __('subjects.school.widgets.class-announcements'),
                ClassLinks::class => __('subjects.school.widgets.class-links'),
	            ClassPageChat::class => __('school.messages'),
	            LearningDemonstrations::class => __('learning.demonstrations.viewer'),
            ],
            'required' =>
            [
	            ClassPageChat::class,
	            LearningDemonstrations::class,
            ],
	        'optional' =>
	        [
		        ClassAnnouncements::class,
		        ClassLinks::class,
	        ],
        ];
	}

	/**
	 * @inheritDoc
	 */
	public static function canConnectToPeople(): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public static function canConnectToSystem(): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public static function getPath(): string
	{
		return 'classes';
	}

	/**
	 * @inheritDoc
	 */
	public static function canBeConfigured(): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function canConnect(Person $person): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function getConnectionClass(): string
	{
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function canSystemConnect(): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function getSystemConnectionClass(): string
	{
		return LocalClassesConnection::class;
	}

	/**
	 * @inheritDoc
	 */
	public function canRegister(): bool
	{
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function registrationUrl(): string
	{
		return '';
	}

	/**
	 * @inheritDoc
	 */
	public function systemAutoconnect(): bool
	{
		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function configurationUrl(): string
	{
		return route(Integrator::INTEGRATOR_ACTION_PREFIX . 'local.classes.index');
	}

    public function canEnable(): bool
    {
        return true;
    }
}