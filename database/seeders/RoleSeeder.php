<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    private array $baseRolePermissions =
        [
            "DB Editor" => ['people.assign.roles', 'people.create', 'people.delete', 'people.edit', 'people.merge', 'people.view'],
            "CRUD Editor" => ['crud'],
            "Impersonator" => [],
            "Web Designer" => [],
            "Role Editor" => [],
            "Permission Editor" => [],
            "Person Contact Editor" => [],
        ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(SchoolRoles::$baseRolePermissions as $roleName => $permissions)
            SchoolRoles::create(['name' => $roleName, 'base_role' => true])
                ->syncPermissions($permissions);

        foreach($this->baseRolePermissions as $roleName => $permissions)
            SchoolRoles::create(['name' => $roleName])->syncPermissions($permissions);

    }
}
