<?php

namespace Tests\Feature\Substitutes;

use App\Mail\NewSubstituteVerification;
use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Utilities\SchoolPermission;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class SubstitutePoolCreatePageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_creates_a_new_substitute_from_a_new_person_record(): void
    {
        Mail::fake();

        $admin = $this->substituteManager();
        $campus = $this->campus('North Campus', 'NORTH');

        $this->actingAs($admin);

        $component = Livewire::test('pages::substitutes.pool.create')
            ->set('first', 'Taylor')
            ->set('last', 'Morgan')
            ->set('email', 'taylor.morgan@example.com')
            ->set('campusIds', [(string) $campus->id])
            ->set('sendVerificationEmail', true)
            ->call('save');

        $person = Person::query()
            ->where('email', 'taylor.morgan@example.com')
            ->first();

        $this->assertNotNull($person);
        $component->assertRedirect(route('features.substitutes.pool.show', $person->substituteProfile, absolute: false));
        $this->assertTrue($person->hasRole(SchoolRoles::$SUBSTITUTE));
        $this->assertNotNull($person->substituteProfile);
        $this->assertSame([$campus->id], $person->substituteProfile->campuses()->pluck('campuses.id')->all());

        Mail::assertSent(NewSubstituteVerification::class, function (NewSubstituteVerification $mail) use ($person) {
            return $mail->hasTo($person->email);
        });
    }

    public function test_it_imports_an_existing_person_without_creating_a_duplicate_record(): void
    {
        Mail::fake();

        $admin = $this->substituteManager();
        $campus = $this->campus('South Campus', 'SOUTH');
        $existingPerson = Person::factory()->create([
            'first' => 'Jordan',
            'last' => 'Lee',
            'email' => 'jordan.lee@example.com',
        ]);

        $this->actingAs($admin);

        $component = Livewire::test('pages::substitutes.pool.create')
            ->call('selectExistingPerson', $existingPerson->id)
            ->set('campusIds', [(string) $campus->id])
            ->set('sendVerificationEmail', false)
            ->call('save');

        $component->assertRedirect(route('features.substitutes.pool.show', $existingPerson->fresh()->substituteProfile, absolute: false));
        $this->assertSame(1, Person::query()->where('email', 'jordan.lee@example.com')->count());
        $this->assertTrue($existingPerson->fresh()->hasRole(SchoolRoles::$SUBSTITUTE));
        $this->assertNotNull($existingPerson->fresh()->substituteProfile);
        $this->assertSame(
            [$campus->id],
            $existingPerson->fresh()->substituteProfile->campuses()->pluck('campuses.id')->all()
        );

        Mail::assertNothingSent();
    }

    public function test_it_blocks_new_person_creation_when_the_email_already_exists(): void
    {
        Mail::fake();

        $admin = $this->substituteManager();
        $campus = $this->campus('East Campus', 'EAST');
        Person::factory()->create([
            'first' => 'Alex',
            'last' => 'Rivera',
            'email' => 'alex.rivera@example.com',
        ]);

        $this->actingAs($admin);

        Livewire::test('pages::substitutes.pool.create')
            ->set('first', 'Alex')
            ->set('last', 'Rivera')
            ->set('email', 'alex.rivera@example.com')
            ->set('campusIds', [(string) $campus->id])
            ->call('save')
            ->assertHasErrors(['email']);

        $this->assertSame(1, Person::query()->where('email', 'alex.rivera@example.com')->count());
        Mail::assertNothingSent();
    }

    private function substituteManager(): Person
    {
        $permission = SchoolPermission::query()->firstOrCreate([
            'name' => 'substitute.admin',
            'guard_name' => 'web',
        ]);

        SchoolRoles::query()->firstOrCreate([
            'name' => SchoolRoles::$SUBSTITUTE,
            'guard_name' => 'web',
        ]);

        SchoolRoles::query()->firstOrCreate([
            'name' => SchoolRoles::$OLD_SUBSTITUTE,
            'guard_name' => 'web',
        ]);

        $admin = Person::factory()->create();
        $admin->givePermissionTo($permission);

        return $admin;
    }

    private function campus(string $name, string $abbr): Campus
    {
        return Campus::query()->create([
            'name' => $name,
            'abbr' => $abbr,
            'order' => 1,
        ]);
    }
}
