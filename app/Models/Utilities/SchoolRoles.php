<?php

namespace App\Models\Utilities;

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
    public static string $INTERNAL_USER = "Internal User";
    public static string $EXTERNAL_USER = "External User";

    public static array $defaultPermissions =
        [
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
        return SchoolRoles::$defaultPermissions[$role] ?? [];
    }

    public static function reservedRoles(): array
    {
        return
        [
            SchoolRoles::$ADMIN,
            SchoolRoles::$EMPLOYEE,
            SchoolRoles::$STUDENT,
            SchoolRoles::$FACULTY,
            SchoolRoles::$STAFF,
            SchoolRoles::$COACH,
            SchoolRoles::$PARENT,
            SchoolRoles::$OLD_STUDENT,
            SchoolRoles::$OLD_FACULTY,
            SchoolRoles::$OLD_STAFF,
            SchoolRoles::$OLD_COACH,
            SchoolRoles::$OLD_PARENT,
            SchoolRoles::$INTERNAL_USER,
            SchoolRoles::$EXTERNAL_USER,
        ];
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
        self::$defaultPermissions = $defaultPermissions;
    }
}
