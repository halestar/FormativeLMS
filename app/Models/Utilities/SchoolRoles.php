<?php

namespace App\Models\Utilities;

use Illuminate\Database\Eloquent\Builder;
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
            "Staff" => ['settings.permissions.view', 'settings.roles.view','crud'],
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
}
