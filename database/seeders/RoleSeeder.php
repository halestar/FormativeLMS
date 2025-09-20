<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private array $baseRolePermissions =
        [
            "DB Editor" =>
                [
                    'people.assign.roles', 'people.create', 'people.delete',
                    'people.edit', 'people.merge', 'people.view', 'school',
                    'people.ids.manage', 'school.emails', 'settings.integrators'
                ],
            "CRUD Editor" => ['crud'],
            "Impersonator" => [],
            "Web Designer" => [],
            "Role Editor" => [],
            "Permission Editor" => [],
            "Person Contact Editor" => [],
            "Academic Manager" =>
                [
                    'subjects.subjects', 'subjects.courses', 'subjects.classes', 'classes.enrollment',
                    'school.tracker.admin'
                ],
            "Locations Manager" =>
                [
                    'locations.campuses', 'locations.years', 'locations.terms', 'locations.buildings',
                    'locations.areas', 'locations.rooms'
                ],
            "Schedule Manager" => ['locations.periods', 'locations.blocks', 'classes.enrollment'],
            "Student Tracker" => ['school.tracker'],
            "Skills Administrator" => ['subjects.skills'],
        ];

    private string $employeeField = '{"pronouns": {"fieldId": "pronouns", "fieldHelp": "Personal pronouns", "fieldName": "Pronouns", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["He/Him", "She/Her", "They/Them"], "fieldPlaceholder": ""}, "extension": {"fieldId": "extension", "fieldHelp": "Employee Extension", "fieldName": "Extension", "fieldType": "TEXT", "fieldValue": null, "fieldOptions": [], "fieldPlaceholder": "ext"}}';
    private string $studentField = '{"race": {"fieldId": "race", "fieldHelp": "Race", "fieldName": "Race", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["American Indian or Alaska Native", "Asian", "Black or African American", "Native Hawaiian or Other Pacific Islander", "White"], "fieldPlaceholder": ""}, "gender": {"fieldId": "gender", "fieldHelp": "Gender", "fieldName": "Gender", "fieldType": "RADIO", "fieldValue": null, "fieldOptions": ["Male", "Female", "Non-Binary"], "fieldPlaceholder": []}, "pronouns": {"fieldId": "pronouns", "fieldHelp": "Personal pronouns", "fieldName": "Pronouns", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["He/Him", "She/Her", "They/Them"], "fieldPlaceholder": ""}, "ethnicity": {"fieldId": "ethnicity", "fieldHelp": "Student ethnicity", "fieldName": "Ethnicity", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["Hispanic or Latino", "Not Hispanic or Latino"], "fieldPlaceholder": ""}}';
    private string $parentField = '{"race": {"fieldId": "race", "fieldHelp": "Race", "fieldName": "Race", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["American Indian or Alaska Native", "Asian", "Black or African American", "Native Hawaiian or Other Pacific Islander", "White"], "fieldPlaceholder": ""}, "title": {"fieldId": "title", "fieldHelp": "", "fieldName": "Title", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["Mr.", "Ms.", "Mrs.", "Dr.", ""], "fieldPlaceholder": ""}, "pronouns": {"fieldId": "pronouns", "fieldHelp": "Personal pronouns", "fieldName": "Pronouns", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["He/Him", "She/Her", "They/Them"], "fieldPlaceholder": ""}, "ethnicity": {"fieldId": "ethnicity", "fieldHelp": "Student ethnicity", "fieldName": "Ethnicity", "fieldType": "SELECT", "fieldValue": null, "fieldOptions": ["Hispanic or Latino", "Not Hispanic or Latino"], "fieldPlaceholder": ""}}';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(SchoolRoles::$baseRolePermissions as $roleName => $permissions)
            SchoolRoles::create(['name' => $roleName, 'base_role' => true])
                ->syncPermissions($permissions);
        SchoolRoles::where('name', SchoolRoles::$EMPLOYEE)
            ->update(['fields' => $this->employeeField]);
        SchoolRoles::where('name', SchoolRoles::$STUDENT)
            ->update(['fields' => $this->studentField]);
        SchoolRoles::where('name', SchoolRoles::$PARENT)
            ->update(['fields' => $this->parentField]);


        foreach($this->baseRolePermissions as $roleName => $permissions)
            SchoolRoles::create(['name' => $roleName])->syncPermissions($permissions);

    }
}
