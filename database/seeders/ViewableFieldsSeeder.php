<?php

namespace Database\Seeders;

use App\Models\CRUD\ViewableGroup;
use App\Models\People\Address;
use App\Models\People\Person;
use App\Models\People\PersonalAddress;
use App\Models\People\Phone;
use App\Models\People\ViewPolicies\ViewableField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ViewableFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ViewableField::insert(
            [
                ['id' => '1', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.first'), 'field' => 'first',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 1],

                ['id' => '2', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.middle'), 'field' => 'middle',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 2],

                ['id' => '3', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.last'), 'field' => 'last',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 3],

                ['id' => '4', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.nick'), 'field' => 'nick',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 4],

                ['id' => '5', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.email'), 'field' => 'email',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 5],

                ['id' => '6', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.dob'), 'field' => 'dob',
                    'parent_class' => Person::class, 'format_as_date' => true, 'format_as_datetime' => false, 'order' => 6],

                ['id' => '7', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.ethnicity'), 'field' => 'ethnicity',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 7],

                ['id' => '8', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.title'), 'field' => 'title',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 8],

                ['id' => '9', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.suffix'), 'field' => 'suffix',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 9],

                ['id' => '10', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.honors'), 'field' => 'honors',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 10],

                ['id' => '11', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.gender'), 'field' => 'gender',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 11],

                ['id' => '12', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.pronouns'), 'field' => 'pronouns',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 12],

                ['id' => '13', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.occupation'), 'field' => 'occupation',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 13],

                ['id' => '14', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.job_title'), 'field' => 'job_title',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 14],

                ['id' => '15', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.work_company'), 'field' => 'work_company',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 15],

                ['id' => '16', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.salutation'), 'field' => 'salutation',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 16],

                ['id' => '17', 'group_id' => ViewableGroup::BASIC_INFO, 'name' => __('people.profile.fields.family_salutation'), 'field' => 'family_salutation',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 17],

                ['id' => '18', 'group_id' => ViewableGroup::HIDDEN, 'name' => __('people.profile.image'), 'field' => 'portrait_url',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 18],

                ['id' => '19', 'group_id' => ViewableGroup::CONTACT_INFO, 'name' => __('addresses.address'), 'field' => 'prettyAddress',
                    'parent_class' => Address::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 1],

                ['id' => '20', 'group_id' => ViewableGroup::CONTACT_INFO, 'name' => __('phones.phone'), 'field' => 'prettyphone',
                    'parent_class' => Phone::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 2],

                ['id' => '21', 'group_id' => ViewableGroup::RELATIONSHIPS, 'name' => __('people.relationships'), 'field' => 'relationships',
                    'parent_class' => Person::class, 'format_as_date' => false, 'format_as_datetime' => false, 'order' => 1],
            ]
        );
    }
}
