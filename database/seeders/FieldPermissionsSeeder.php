<?php

namespace Database\Seeders;

use App\Models\People\FieldPermission;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Seeder;

class FieldPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * first, we add all the base permissions. These will be required by the base system.
         * The base fields are all the base properties of the Person object that are hard
         * coded into the database.
         * They are: first, middle, last, email, nick, dob, portrait (includes both url and thumb)
         * addresses, phones and relationships.
         * Note that name is NOT there, name will ALWAYS be an accessible field.
         */

        FieldPermission::insert(
            [
                [
                    'field' => 'first',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'middle',
                    'by_self' => true,
                    'by_employees' => false,
                    'by_students' => false,
                    'by_parents' => false,
                ],
                [
                    'field' => 'last',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'email',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'nick',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'preferred_first',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'dob',
                    'by_self' => true,
                    'by_employees' => false,
                    'by_students' => false,
                    'by_parents' => false,
                ],
                [
                    'field' => 'portrait',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
                [
                    'field' => 'addresses',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => false,
                    'by_parents' => false,
                ],
                [
                    'field' => 'phones',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => false,
                    'by_parents' => false,
                ],
                [
                    'field' => 'relationships',
                    'by_self' => true,
                    'by_employees' => true,
                    'by_students' => true,
                    'by_parents' => true,
                ],
            ]);

        //next, we do all the custom fields.
        foreach(SchoolRoles::whereNotNull('fields')->get() as $role)
        {
            foreach($role->fields as $field)
            {
                FieldPermission::create(
                    [
                        'field' => $field->fieldId,
                        'role_id' => $role->id,
                        'by_self' => true,
                    ]);
            }
        }
    }
}
