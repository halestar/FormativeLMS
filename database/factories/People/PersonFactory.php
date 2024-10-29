<?php

namespace Database\Factories\People;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Relationship;
use App\Models\People\Person;
use App\Models\People\PersonalRelations;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first' => fake()->firstName(),
            'middle' => null,
            'last' => fake()->lastName(),
            'email' => fake()->userName() . config('lms.internal_email_suffix') ,
            'nick' => null,
            'dob' => fake()->dateTimeBetween('-18 years', 'now'),
            'password' => Hash::make(fake()->password(20, 30)),
            'ethnicity_id' => Ethnicity::inRandomOrder()->first()->id,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'pronoun_id' => Pronouns::inRandomOrder()->first()->id,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'portrait_url' => $this->faker->imageUrl(),
            'thumbnail_url' => $this->faker->imageUrl(64, 64)
        ];
    }

    /**
     * EMPLOYEE FUNCTIONS
     */

    public function faculty(): static
    {
        return $this->state(function (array $attributes)
                    {
                        return
                            [
                                'job_title' =>  'Faculty',
                                'work_company' => 'My School',
                                'occupation' => 'Teacher',
                            ];
                    })
                ->afterCreating(function (Person $person)
                {
                    $person->assignRole(SchoolRoles::$FACULTY);
                    $person->assignRole(SchoolRoles::$EMPLOYEE);
                });
    }

    public function staff(): Factory
    {
        return $this->state(function (array $attributes)
        {
            return
                [
                    'job_title' =>  'Staff',
                    'work_company' => 'My School',
                    'occupation' => 'Staff',
                ];
        })
            ->afterCreating(function (Person $person)
            {
                $person->assignRole(SchoolRoles::$STAFF);
                $person->assignRole(SchoolRoles::$EMPLOYEE);
            });
    }

    public function coach(): Factory
    {
        return $this->state(function (array $attributes)
        {
            return
                [
                    'job_title' =>  'Coach',
                    'work_company' => 'My School',
                    'occupation' => 'Coach',
                ];
        })
            ->afterCreating(function (Person $person)
            {
                $person->assignRole(SchoolRoles::$COACH);
                $person->assignRole(SchoolRoles::$EMPLOYEE);
            });
    }

    /**
     * STUDENT FUNCTIONS
     */

    public function student(): Factory
    {
        return $this->state(function (array $attributes)
                    {
                        return
                            [
                                'dob' => fake()->dateTimeBetween('-18 years', '-4 years'),
                            ];
                    })
                ->afterCreating(function (Person $person)
                {
                    $person->assignRole(SchoolRoles::$STUDENT);
                });
    }

    public function attachParents(): Factory
    {
        return $this->afterCreating(function (Person $person)
        {
            //for each relationship that is a CHILD relationship, we will add a reverse relationship of type PARENT
            foreach($person->relationships as $relationship)
            {
                if($relationship->personal->relationship_id == Relationship::CHILD)
                    $relationship->relationships()->attach($person->id, ['relationship_id' => Relationship::PARENT]);
            }

        });
    }

    public function sharePrimaryAddress(): Factory
    {
        return $this->afterCreating(function (Person $person)
        {
            // for each child relationship, share the primary address.
            foreach($person->relationships()->wherePivot('relationship_id', Relationship::CHILD)->get() as $relationship)
            {
                $relationship->addresses()->attach($person->primaryAddress()->id, ['primary' => true]);
            }
        });
    }

    public function sharePrimaryPhone(): Factory
    {
        return $this->afterCreating(function (Person $person)
        {
            // for each child relationship, share the primary phone.
            foreach($person->relationships()->wherePivot('relationship_id', Relationship::CHILD)->get() as $relationship)
            {
                $relationship->phones()->attach($person->primaryPhone()->id, ['primary' => true]);
            }
        });
    }

    /**
     * PARENT FUNCTIONS
     */

    public function parents(): Factory
    {
        return $this->state(function (array $attributes)
                        {
                            return
                                [
                                    'email' => fake()->email(),
                                    'job_title' =>  $this->faker->jobTitle(),
                                    'work_company' => $this->faker->company(),
                                    'occupation' => $this->faker->jobTitle(),
                                ];
                        })
                    ->afterCreating(function (Person $person)
                    {
                        $person->assignRole(SchoolRoles::$PARENT);
                    });
    }


    /**
     * GLOBAL FUNCTIONS
     */

    public function middleName(): Factory
    {
        return $this->state(function (array $attributes)
        {
            return ['middle' => fake()->firstName];
        });
    }

    public function nick(): Factory
    {
        return $this->state(function (array $attributes)
        {
            return ['nick' => fake()->firstName ];
        });
    }
}
