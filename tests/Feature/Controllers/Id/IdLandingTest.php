<?php

namespace Feature\Controllers\Id;

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class IdLandingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('people.school-ids.show');
	}

	protected function global(): string
	{
		return route('people.school-ids.manage.global');
	}

	protected function byCampus(): string
	{
		$campus = Campus::first();
		return route('people.school-ids.manage.campus', $campus);
	}

	protected function byRole(): string
	{
		$role = SchoolRoles::StudentRole();
		return route('people.school-ids.manage.role', $role);
	}

	protected function byRoleAndCampus(): string
	{
		$role = SchoolRoles::StudentRole();
		$campus = Campus::first();
		return route('people.school-ids.manage.both', ['role' => $role->id, 'campus' => $campus->id]);
	}

	/**
	 * Index
	 */
	public function test_landing_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}
    public function test_landing_staff(): void
    {
		$staff = Person::where('email', 'staff@kalinec.net')->first();
        $response = $this->actingAs($staff)
	        ->get($this->index());
        $response->assertStatus(Response::HTTP_OK);
    }
	public function test_landing_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_landing_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_landing_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->index());
		$response->assertStatus(Response::HTTP_OK);
	}

	public function test_landing_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->index());
		$response->assertRedirect(route('login'));
	}

	/**
	 * global
	 */
	public function test_global_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->global());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_global_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->global());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_global_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->global());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_global_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->global());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_global_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->global());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_global_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->global());
		$response->assertRedirect(route('login'));
	}

	/**
	 * byCampus
	 */
	public function test_campus_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->byCampus());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_campus_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->byCampus());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_campus_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->byCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_campus_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->byCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_campus_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->byCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_campus_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->byCampus());
		$response->assertRedirect(route('login'));
	}

	/**
	 * byRole
	 */
	public function test_role_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->byRole());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_role_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->byRole());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_role_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->byRole());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_role_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->byRole());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_role_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->byRole());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_role_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->byRole());
		$response->assertRedirect(route('login'));
	}


	/**
	 * byRoleAndCampus
	 */
	public function test_both_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$response = $this->actingAs($admin)
			->get($this->byRoleAndCampus());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_both_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$response = $this->actingAs($staff)
			->get($this->byRoleAndCampus());
		$response->assertStatus(Response::HTTP_OK);
	}
	public function test_both_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$response = $this->actingAs($faculty)
			->get($this->byRoleAndCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_both_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$response = $this->actingAs($student)
			->get($this->byRoleAndCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_both_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$response = $this->actingAs($parent)
			->get($this->byRoleAndCampus());
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	public function test_both_guest(): void
	{
		$response = $this->actingAsGuest()
			->get($this->byRoleAndCampus());
		$response->assertRedirect(route('login'));
	}
}
