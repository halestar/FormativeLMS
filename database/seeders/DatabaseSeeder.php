<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(
            [
                PermissionSeeder::class,
                RoleSeeder::class,
                CrudSeeder::class,
                CampusSeeder::class,
                AdminSeeder::class,
                FacultySeeder::class,
                StaffSeeder::class,
                CoachSeeder::class,
                FamilySeeder::class,
                ViewableFieldsSeeder::class,
                ViewPolicySeeder::class,
            ]);
    }
}
