<?php

namespace Database\Factories\People;

use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Pronouns;
use App\Models\People\Person;
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
            'email' => fake()->safeEmail(),
            'nick' => null,
            'dob' => fake()->dateTimeBetween('-18 years', 'now'),
            'password' => null,
            'ethnicity_id' => Ethnicity::inRandomOrder()->first()->id,
            'gender_id' => Gender::inRandomOrder()->first()->id,
            'pronoun_id' => Pronouns::inRandomOrder()->first()->id,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function faculty(): static
    {
        return $this->state(function (array $attributes)
                    {
                        return
                            [
                                'email' => fake()->userName() . config('lms.internal_email_suffix') ,
                                'password' => Hash::make(fake()->password(20, 30)),
                            ];
                    })
                ->afterCreating(function (Person $person)
                {
                    $person->assignRole(SchoolRoles::$FACULTY);
                    $person->assignRole(SchoolRoles::$EMPLOYEE);
                });
    }

    public function student(): Factory
    {
        return $this->state(function (array $attributes)
                    {
                        return
                            [
                                'email' => fake()->userName() . config('lms.internal_email_suffix') ,
                                'password' => Hash::make(fake()->password(20, 30)),
                            ];
                    })
                ->afterCreating(function (Person $person)
                {
                    $person->assignRole(SchoolRoles::$STUDENT);
                });
    }

    public function staff(): Factory
    {
        return $this->state(function (array $attributes)
                        {
                            return
                                [
                                    'email' => fake()->userName() . config('lms.internal_email_suffix') ,
                                    'password' => Hash::make(fake()->password(20, 30)),
                                ];
                        })
                    ->afterCreating(function (Person $person)
                    {
                        $person->assignRole(SchoolRoles::$STAFF);
                        $person->assignRole(SchoolRoles::$EMPLOYEE);
                    });
    }

    public function parents(): Factory
    {
        return $this->state(function (array $attributes)
                        {
                            return
                                [
                                    'email' => fake()->email(),
                                    'password' => Hash::make(fake()->password(20, 30)),
                                ];
                        })
                    ->afterCreating(function (Person $person)
                    {
                        $person->assignRole(SchoolRoles::$PARENT);
                    });
    }

    public function coach(): Factory
    {
        return $this->state(function (array $attributes)
                    {
                        return
                            [
                                'email' => fake()->userName() . config('lms.internal_email_suffix') ,
                                'password' => Hash::make(fake()->password(20, 30)),
                            ];
                    })
                ->afterCreating(function (Person $person)
                {
                    $person->assignRole(SchoolRoles::$COACH);
                    $person->assignRole(SchoolRoles::$EMPLOYEE);
                });
    }

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
