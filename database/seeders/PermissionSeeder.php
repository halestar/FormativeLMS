<?php

namespace Database\Seeders;

use App\Models\Utilities\PermissionCategory;
use App\Models\Utilities\SchoolPermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    private static array $categories =
        [
            'Settings',
            'Personal Information'
        ];
    private static array $permissions =
        [
            'Settings' =>
                [
                    ['name' => 'settings.permissions', 'description' => 'Change permissions in the system'],
                    ['name' => 'settings.roles', 'description' => 'Change roles in the system'],
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
