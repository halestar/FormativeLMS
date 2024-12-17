<?php

namespace App\Models\Utilities;

use App\Classes\RoleField;
use App\Models\People\RoleFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role;

class SchoolRoles extends Role
{
    public static string $ADMIN = "Super Admin";
    public static string $EMPLOYEE = "Employee";
    public static string $STUDENT = "Student";
    public static string $FACULTY = "Faculty";
    public static string $STAFF = "Staff";
    public static string $COACH = "Coach";
    public static string $PARENT = "Parent";
    public static string $OLD_STUDENT = "Old Student";
    public static string $OLD_FACULTY = "Old Faculty";
    public static string $OLD_STAFF = "Old Staff";
    public static string $OLD_COACH = "Old Coach";
    public static string $OLD_PARENT = "Old Parent";

    protected function casts(): array
    {
        return [
            'base_role' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('base_name_order', function (Builder $builder)
        {
            $builder->orderBy('base_role')->orderBy('name');
        });
    }

    public function scopeBaseRoles(Builder $query): void
    {
        $query->where('base_role', true);
    }

    public function scopeNormalRoles(Builder $query): void
    {
        $query->where('base_role', false);
    }

    public function scopeExcludeAdmin(Builder $query): void
    {
        $query->where('name', '<>', SchoolRoles::$ADMIN);
    }

    public static array $baseRolePermissions =
        [
            "Super Admin" => [],
            "Student" => [],
            "Employee" => [],
            "Faculty" => [],
            "Staff" =>
                [
                    'settings.permissions.view', 'settings.roles.view', 'crud',
                    'locations.campuses', 'locations.years', 'locations.terms',
                    'locations.buildings', 'locations.areas', 'locations.rooms',
                ],
            "Coach" => [],
            "Parent" => [],
            "Old Student" => [],
            "Old Faculty" => [],
            "Old Staff" => [],
            "Old Coach" => [],
            "Old Parent" => [],
        ];

    public static function getDefaultPermissions(string $role): array
    {
        return SchoolRoles::$baseRolePermissions[$role] ?? [];
    }

    public static function EmployeeRole(): SchoolRoles
    {
        return SchoolRoles::where('name', '=', self::$EMPLOYEE)->first();
    }

    public static function StudentRole(): SchoolRoles
    {
        return SchoolRoles::where('name', '=', self::$STUDENT)->first();
    }

    public static function ParentRole(): SchoolRoles
    {
        return SchoolRoles::where('name', '=', self::$PARENT)->first();
    }

    public static function setDefaultPermissions(array $defaultPermissions): void
    {
        self::$baseRolePermissions = $defaultPermissions;
    }

    protected function fields(): Attribute
    {
        if($this->pivot)
            $fieldValues = json_decode($this->pivot->field_values, true);
        else
            $fieldValues = null;
        return Attribute::make(
            get: function(?string $value) use ($fieldValues)
            {
                if(!$value)
                    return [];
                $fields = [];
                $value = json_decode($value, true);
                foreach($value as $field)
                {
                    if($fieldValues)
                        $field['fieldValue'] = $fieldValues[$field['fieldId']]?? null;
                    $fields[$field['fieldId']] = new RoleField($field);
                }
                return $fields;
            },
            set: function (?array $value)
            {
                if(!$value)
                    return json_encode([]);
                $fields = [];
                foreach($value as $field)
                    $fields[$field->fieldId] = $field->toArray();
                return json_encode($fields);
            }
        );
    }
}
