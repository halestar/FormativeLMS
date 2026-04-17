<?php

namespace Tests\Feature\Substitutes;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\People\Phone;
use App\Models\Substitutes\Substitute;
use App\Models\Utilities\SchoolPermission;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubstitutePoolEditPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_updates_the_substitute_phone_selection_and_campuses(): void
    {
        $admin = $this->substituteManager();
        $person = Person::factory()->create();
        $substitute = Substitute::query()->create([
            'person_id' => $person->id,
            'active' => true,
        ]);

        $currentPhone = Phone::factory()->create();
        $newPhone = Phone::factory()->mobile()->create();
        $otherPhone = Phone::factory()->create();
        $firstCampus = $this->campus('North Campus', 'NORTH');
        $secondCampus = $this->campus('South Campus', 'SOUTH', 2);

        $person->phones()->attach($currentPhone, ['primary' => true, 'label' => 'Home', 'order' => 0]);
        $person->phones()->attach($newPhone, ['primary' => false, 'label' => 'Mobile', 'order' => 1]);
        $substitute->campuses()->attach($firstCampus);

        $outsider = Person::factory()->create();
        $outsider->phones()->attach($otherPhone, ['primary' => true, 'label' => 'Other', 'order' => 0]);

        $this->actingAs($admin)
            ->put(route('features.substitutes.pool.update', $substitute), [
                'phone_id' => $newPhone->id,
                'campuses' => [$secondCampus->id],
            ])
            ->assertRedirect(route('features.substitutes.pool.show', $substitute, absolute: false))
            ->assertSessionHas('status', __('features.substitutes.pool.updated'));

        $this->assertSame($newPhone->id, $substitute->fresh()->phone_id);
        $this->assertSame([$secondCampus->id], $substitute->fresh()->campuses()->pluck('campuses.id')->all());

        $this->actingAs($admin)
            ->from(route('features.substitutes.pool.edit', $substitute, absolute: false))
            ->put(route('features.substitutes.pool.update', $substitute), [
                'phone_id' => $otherPhone->id,
                'campuses' => [$secondCampus->id],
            ])
            ->assertRedirect(route('features.substitutes.pool.edit', $substitute, absolute: false))
            ->assertSessionHasErrors(['phone_id']);

        $this->assertSame($newPhone->id, $substitute->fresh()->phone_id);
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

    private function campus(string $name, string $abbr, int $order = 1): Campus
    {
        return Campus::query()->create([
            'name' => $name,
            'abbr' => $abbr,
            'order' => $order,
        ]);
    }
}