<?php

namespace App\Classes\Integrators\Local\Services;

use App\Classes\Integrators\Local\Connections\LocalClassesConnection;
use App\Enums\IntegratorServiceTypes;
use App\Livewire\School\ClassManagement\ClassAnnouncements;
use App\Livewire\School\ClassManagement\ClassLinks;
use App\Livewire\School\ClassManagement\ClassPageChat;
use App\Livewire\School\ClassManagement\ClassRoster;
use App\Livewire\School\ClassManagement\ClassSchedule;
use App\Livewire\School\ClassManagement\LearningDemonstrations;
use App\Models\Integrations\Integrator;
use App\Models\Integrations\LmsIntegrationService;
use App\Models\People\Person;

class LocalClassesService extends LmsIntegrationService
{
    /**
     * {@inheritDoc}
     */
    public static function getServiceType(): IntegratorServiceTypes
    {
        return IntegratorServiceTypes::CLASSES;
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceName(): string
    {
        return __('integrators.local.classes');
    }

    /**
     * {@inheritDoc}
     */
    public static function getServiceDescription(): string
    {
        return __('integrators.local.classes.description');
    }

    /**
     * {@inheritDoc}
     */
    public static function getDefaultData(): array
    {
        return
        [
            'available' => [
                ClassAnnouncements::class => __('subjects.school.widgets.class-announcements'),
                ClassLinks::class => __('subjects.school.widgets.class-links'),
                ClassPageChat::class => __('school.messages'),
                LearningDemonstrations::class => __('learning.demonstrations.viewer'),
                ClassSchedule::class => __('subjects.class.schedule'),
                ClassRoster::class => __('school.classes.roster'),
            ],
            'required' => [
                ClassPageChat::class,
                LearningDemonstrations::class,
            ],
            'optional' => [
                ClassAnnouncements::class,
                ClassLinks::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function canConnectToPeople(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public static function canConnectToSystem(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public static function getPath(): string
    {
        return 'classes';
    }

    public function canEnable(): bool
    {
        return true;
    }

    public function getConnectionClass(): string
    {
        return LocalClassesConnection::class;
    }

    public function canConnect(?Person $person = null): bool
    {
        return $person == null;
    }

    public function canRegister(?Person $person = null): bool
    {
        return false;
    }

    public function canConfigure(?Person $person = null): bool
    {
        return true;
    }

    public function registrationUrl(?Person $person = null): ?string
    {
        return null;
    }

    public function configurationUrl(?Person $person = null): ?string
    {
        return route(Integrator::INTEGRATOR_ACTION_PREFIX.'local.classes.index');
    }
}
