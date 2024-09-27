<?php

namespace Database\Seeders;

use App\Enums\PolicyType;
use App\Models\People\ViewPolicies\ViewableField;
use App\Models\People\ViewPolicies\ViewPolicy;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ViewPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $first = 1;
        $middle = 2;
        $last = 3;
        $nick = 4;
        $email = 5;
        $dob = 6;
        $ethnicity = 7;
        $title = 8;
        $suffix = 9;
        $honors = 10;
        $gender = 11;
        $pronouns = 12;
        $occupation = 13;
        $job_title = 14;
        $work_company = 15;
        $salutation = 16;
        $family_salutation = 17;
        $profile_img = 18;

        // Employees
        $policy = new ViewPolicy();
        $policy->id = 1;
        $policy->name = __('people.policies.employee');
        $policy->role_id = Role::findByName(SchoolRoles::$EMPLOYEE)->id;
        $policy->base_role = PolicyType::EMPLOYEE;
        $policy->save();
        // Basic Info
        $policy->fields()->attach($first,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($middle,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($last,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($nick,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($email,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($dob,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($ethnicity,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($title,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($suffix,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($honors,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($gender,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($pronouns,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($occupation,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($job_title,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($work_company,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_viewable' => true,
                'student_viewable' => true,
                'parent_viewable' => true,
            ]);
        $policy->fields()->attach($salutation,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($family_salutation,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($profile_img,
            [
               'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);



        // Students
        $policy = new ViewPolicy();
        $policy->id = 2;
        $policy->name = __('people.policies.student');
        $policy->role_id = Role::findByName(SchoolRoles::$STUDENT)->id;
        $policy->base_role = PolicyType::STUDENT;
        $policy->save();
        // Basic Info
        $policy->fields()->attach($first,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => false, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($middle,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($last,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($nick,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($email,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($dob,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($ethnicity,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($title,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($suffix,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($honors,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($gender,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($pronouns,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($occupation,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($job_title,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($work_company,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($salutation,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($family_salutation,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($profile_img,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => true, 'student_viewable' => true,
                'parent_enforce' => true, 'parent_viewable' => true,
            ]);

        // Parents
        $policy = new ViewPolicy();
        $policy->id = 3;
        $policy->name = __('people.policies.parent');
        $policy->role_id = Role::findByName(SchoolRoles::$PARENT)->id;
        $policy->base_role = PolicyType::PARENT;
        $policy->save();
        // Basic Info
        $policy->fields()->attach($first,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($middle,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($last,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($nick,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($email,
            [
                'self_viewable' => true, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($dob,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($ethnicity,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($title,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($suffix,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($honors,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($gender,
            [
                'self_viewable' => false, 'editable' => false,
                'employee_enforce' => true, 'employee_viewable' => false,
                'student_enforce' => true, 'student_viewable' => false,
                'parent_enforce' => true, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($pronouns,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
        $policy->fields()->attach($occupation,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($job_title,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($work_company,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => true, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($salutation,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($family_salutation,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => false,
                'student_enforce' => false, 'student_viewable' => false,
                'parent_enforce' => false, 'parent_viewable' => false,
            ]);
        $policy->fields()->attach($profile_img,
            [
                'self_viewable' => true, 'editable' => true,
                'employee_enforce' => false, 'employee_viewable' => true,
                'student_enforce' => false, 'student_viewable' => true,
                'parent_enforce' => false, 'parent_viewable' => true,
            ]);
    }
}
