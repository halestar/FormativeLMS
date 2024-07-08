<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => SchoolRoles::$ADMIN])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$ADMIN));
        Role::create(['name' => SchoolRoles::$STUDENT])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$STUDENT));
        Role::create(['name' => SchoolRoles::$FACULTY])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$FACULTY));
        Role::create(['name' => SchoolRoles::$STAFF])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$STAFF));
        Role::create(['name' => SchoolRoles::$COACH])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$COACH));
        Role::create(['name' => SchoolRoles::$PARENT])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$PARENT));
        Role::create(['name' => SchoolRoles::$OLD_STUDENT])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$OLD_STUDENT));
        Role::create(['name' => SchoolRoles::$OLD_FACULTY])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$OLD_FACULTY));
        Role::create(['name' => SchoolRoles::$OLD_STAFF])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$OLD_STAFF));
        Role::create(['name' => SchoolRoles::$OLD_COACH])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$OLD_COACH));
        Role::create(['name' => SchoolRoles::$OLD_PARENT])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$OLD_PARENT));
        Role::create(['name' => SchoolRoles::$INTERNAL_USER])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$INTERNAL_USER));
        Role::create(['name' => SchoolRoles::$EXTERNAL_USER])->syncPermissions(SchoolRoles::getDefaultPermissions(SchoolRoles::$EXTERNAL_USER));
    }
}
