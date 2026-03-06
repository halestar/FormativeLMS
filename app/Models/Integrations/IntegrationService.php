<?php

namespace App\Models\Integrations;

use App\Casts\Utilities\AsJsonData;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\HasSchoolRoles;
use App\Models\People\Person;
use App\Traits\HasSchoolRolesTrait;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IntegrationService extends Model implements HasSchoolRoles
{
    use HasFactory;
    /*************************************************
     * PROPERTIES
     */
    use HasSchoolRolesTrait
    {
        HasSchoolRolesTrait::hasRole as traitHasRole;
        HasSchoolRolesTrait::schoolRoles as traitSchoolRoles;
    }

    public $timestamps = false;

    public $incrementing = true;

    protected $table = 'integration_services';

    protected $primaryKey = 'id';

    protected $fillable =
        [
            'name',
            'className',
            'path',
            'description',
            'service_type',
            'data',
            'enabled',
            'can_connect_to_people',
            'can_connect_to_system',
            'configurable',
            'inherit_permissions',
        ];

    protected $guard_name = 'web';

    public function __toString()
    {
        return $this->name;
    }

    /*****************************************************************
     * OVERRIDES
     */

    public function newFromBuilder($attributes = [], $connection = null)
    {
        if ($attributes instanceof \stdClass) {
            $attributes = json_decode(json_encode($attributes), true);
        }
        if ($attributes['className'] == static::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        return (new $attributes['className'])->newFromBuilder($attributes, $connection);
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->inherit_permissions) {
            return $this->integrator->hasRole($roles, $guard);
        }

        return $this->traitHasRole($roles, $guard);
    }

    /*****************************************************************
     * RELATIONSHIPS
     */

    public function integrator(): BelongsTo
    {
        return $this->belongsTo(Integrator::class, 'integrator_id');
    }

    public function personalConnections(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'integration_connections', 'service_id', 'person_id')
            ->wherePivotNotNull('person_id')
            ->withPivot('id', 'data', 'enabled', 'className')
            ->as('lms_service_connection')
            ->using(IntegrationConnection::class);
    }

    public function schoolRoles(): BelongsToMany
    {
        if ($this->inherit_permissions) {
            return $this->integrator->schoolRoles();
        }

        return $this->traitSchoolRoles();
    }

    protected function casts(): array
    {
        return
            [
                'service_type' => IntegratorServiceTypes::class,
                'data' => AsJsonData::class,
                'enabled' => 'boolean',
                'can_connect_to_people' => 'boolean',
                'can_connect_to_system' => 'boolean',
                'configurable' => 'boolean',
                'inherit_permissions' => 'boolean',
            ];
    }

    protected function inheritPermissions(): Attribute
    {
        return Attribute::make(
            get: fn (bool $value) => $value,
            set: function (bool $value) {
                if ($value) {
                    $this->roles()
                        ->detach();
                } else {
                    $this->roles()
                            ->sync($this->integrator->roles->pluck('id')
                                ->toArray());
                }

                return ['inherit_permissions' => $value];
            }
        );
    }

    public function connections(): HasMany
    {
        return $this->hasMany(IntegrationConnection::class, 'service_id');
    }

    /*****************************************************************
     * SCOPES
     */
    #[Scope]
    protected function enabled(Builder $query): void
    {
        $query->where('enabled', true);
    }

    #[Scope]
    protected function personal(Builder $query): void
    {
        $query->where('can_connect_to_people', true);
    }

    #[Scope]
    protected function system(Builder $query): void
    {
        $query->where('can_connect_to_system', true);
    }

    #[Scope]
    protected function ofType(Builder $query, IntegratorServiceTypes $type)
    {
        $query->where('service_type', $type);
    }

    #[Scope]
    protected function configurable(Builder $query)
    {
        $query->where('configurable', true);
    }

    /*****************************************************************
     * CONNECTION METHODS
     */

    /**
     * This function determines whether the system or person is able to establish a connection to this service. In
     * order for this to be true, the following conditions should be met:
     * - The service should be enabled
     * - The service's integrator should be enabled
     * - If it's a person that's trying to connect, it should have the correct roles assigned.
     * - Finally, the actual class must be ok with the connection.
     *
     * @param  Person|null  $person  The person attempting to connect or the system, if null.
     * @return bool Whether the connection is possible
     */
    final protected function ableToConnect(?Person $person = null): bool
    {
        if (! $person) {
            return $this->enabled && $this->integrator->enabled && $this->canConnect($person);
        }

        return $this->enabled && $this->integrator->enabled && $person->hasAnyRole($this->schoolRoles) && $this->canConnect($person);
    }

    /**
     * This function will remove the connection between this service and the person or system.
     * Note that this method can be overridden by subclasses to perform additional actions before or after the connection is removed.
     *
     * @param  Person|null  $person  The person attempting to disconnect or the system, if null.
     */
    public function forgetConnection(?Person $person = null): void
    {
        IntegrationConnection::where('service_id', $this->id)
            ->where('person_id', $person?->id)
            ->delete();
    }

    final public function registerConnection(?Person $person = null, $data = []): IntegrationConnection
    {
        return ($this->getConnectionClass())::updateOrCreate(
            [
                'service_id' => $this->id,
                'person_id' => $person?->id,
            ],
            [
                'data' => array_merge(($this->getConnectionClass())::getInstanceDefault(), $data),
                'className' => $this->getConnectionClass(),
                'enabled' => true,
            ]);
    }

    final public function hasConnection(?Person $person = null)
    {
        return IntegrationConnection::where('service_id', $this->id)
            ->where('person_id', $person?->id)
            ->exists();
    }

    /**
     * Attempts to connect this service to the person or system if the connection is possible.
     *
     * @param  Person|null  $person  The person that is attempting to connect or the system, if null.
     * @return IntegrationConnection|null Returns the connection if it was successfully connected, else null.
     */
    final public function connect(?Person $person = null): ?IntegrationConnection
    {
        // check if we can connect to this person
        if (! $this->ableToConnect($person)) {
            return null;
        }
        // so at this point, we can connect, but first, we check if the connection already exists.
        $connection = IntegrationConnection::where('service_id', $this->id)
            ->where('person_id', $person?->id)
            ->first();
        if ($connection) {
            return $connection;
        }

        return $this->registerConnection($person);
    }
}
