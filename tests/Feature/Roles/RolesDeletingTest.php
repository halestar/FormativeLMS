<?php

namespace Feature\Roles;

use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RolesDeletingTest extends TestCase
{
	use DatabaseTransactions;

	protected function index(): string
	{
		return route('settings.roles.index');
	}

	protected function deleteRoute($id): string
	{
		return route('settings.roles.destroy', $id);
	}

	public function id()
	{
		return 15;
	}

	public function test_delete_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$this->actingAs($admin)
			->delete($this->deleteRoute($this->id()))
			->assertRedirect($this->index());
		$this->assertDatabaseMissing('roles', ['id' => $this->id()]);
	}

	public function test_delete_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$this->actingAs($staff)
			->delete($this->deleteRoute($this->id()))
			->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('roles', ['id' => $this->id()]);
	}

	public function test_delete_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$this->actingAs($faculty)
			->delete($this->deleteRoute($this->id()))
			->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('roles', ['id' => $this->id()]);
	}

	public function test_delete_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$this->actingAs($student)
			->delete($this->deleteRoute($this->id()))
			->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('roles', ['id' => $this->id()]);
	}

	public function test_delete_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$this->actingAs($parent)
			->delete($this->deleteRoute($this->id()))
			->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseHas('roles', ['id' => $this->id()]);
	}

}
