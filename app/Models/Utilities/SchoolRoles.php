<?php

namespace App\Models\Utilities;

use Spatie\Permission\Models\Role;

class SchoolRoles extends Role
{
    public static string $ADMIN = "Super Admin";
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
            "Student" => ['people.view', 'people.search'],
            "Faculty" => ['people.view', 'people.search'],
            "Staff" => ['people.view', 'people.search'],
            "Coach" => ['people.view', 'people.search'],
            "Parent" => ['people.view', 'people.search'],
            "Old Student" => ['people.view', 'people.search'],
            "Old Faculty" => ['people.view', 'people.search'],
            "Old Staff" => ['people.view', 'people.search'],
            "Old Coach" => ['people.view', 'people.search'],
            "Old Parent" => ['people.view', 'people.search'],
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
}
