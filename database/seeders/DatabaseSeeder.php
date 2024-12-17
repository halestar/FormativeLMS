<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                BuildingSeeder::class,
                CampusSeeder::class,
                YearSeeder::class,
                RoomSeeder::class,
                AdminSeeder::class,
                FacultySeeder::class,
                StaffSeeder::class,
                CoachSeeder::class,
                FamilySeeder::class,
                ViewableFieldsSeeder::class,
                ViewPolicySeeder::class,
                SubjectSeeder::class,
                CourseSeeder::class,
            ]);
    }
}
