<?php

namespace Database\Seeders;

use App\Models\Utilities\PermissionCategory;
use App\Models\Utilities\SchoolPermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private static array $categories =
        [
            'Roles/Permissions',
            'Personal Information',
            'System',
        ];
    private static array $permissions =
        [
            'Roles/Permissions' =>
                [
                    ['name' => 'settings.permissions.view', 'description' => 'View permissions in the system'],
                    ['name' => 'settings.permissions.edit', 'description' => 'Edit permissions in the system'],
                    ['name' => 'settings.permissions.create', 'description' => 'Create permissions in the system'],
                    ['name' => 'settings.permissions.delete', 'description' => 'Delete permissions in the system'],
                    ['name' => 'settings.roles.view', 'description' => 'View roles in the system'],
                    ['name' => 'settings.roles.edit', 'description' => 'Edit roles in the system'],
                    ['name' => 'settings.roles.create', 'description' => 'Create roles in the system'],
                    ['name' => 'settings.roles.delete', 'description' => 'Delete roles in the system'],
                ],
            'Personal Information' =>
                [
                    ['name' => 'people.view', 'description' => 'View all people\'s profiles'],
                    ['name' => 'people.edit', 'description' => 'Edit person\'s basic information only'],
                    ['name' => 'people.create', 'description' => 'Create a new Person'],
                    ['name' => 'people.delete', 'description' => 'Soft Delete a person'],
                    ['name' => 'people.merge', 'description' => 'Merge two person records into one.'],
                    ['name' => 'people.search', 'description' => 'Search for people in the system'],
                ],
            'System' =>
                [
                    ['name' => 'cms', 'description' => 'Access to the site\'s CSM'],
                    ['name' => 'crud', 'description' => 'Access to the dmin dashboard for CRUD editors'],
                ],
        ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(static::$categories as $category)
        {
            $cat = PermissionCategory::create(['name' => $category]);
            foreach(static::$permissions[$category] as $permission)
                SchoolPermission::create($permission + ['category_id' => $cat->id]);
        }
    }
}
