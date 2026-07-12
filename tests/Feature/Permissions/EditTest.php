<?php

namespace Tests\Feature\Permissions;

use App\Models\People\Person;
use App\Models\Utilities\SchoolPermission;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditTest extends TestCase
{

	use DatabaseTransactions;

	protected function index(): string
	{
		return route('settings.permissions.index');
	}

	protected function update($id): string
	{
		return route('settings.permissions.update', $id);
	}

	protected function data(SchoolPermission $permission): array
	{
		return
			[
				'category_id' => $permission->category_id,
				'name'        => $permission->name . "_new",
				'description' => $permission->description . "_new",
				'roles'       => $permission->roles->pluck('id')->toArray(),
			];
	}

	protected function compareData(array $data): array
	{
		return
			[
				'category_id' => $data['category_id'],
				'name'        => $data['name'],
				'description' => $data['description'],
			];
	}

	public function test_update_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAs($admin)
		                 ->patch($this->update($permission->id), $this->data($permission));
		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('permissions', $this->compareData($this->data($permission)));
	}

	public function test_update_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAs($staff)
		                 ->patch($this->update($permission->id), $this->data($permission));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->compareData($this->data($permission)));
	}

	public function test_update_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAs($faculty)
		                 ->patch($this->update($permission->id), $this->data($permission));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->compareData($this->data($permission)));
	}

	public function test_update_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAs($student)
		                 ->patch($this->update($permission->id), $this->data($permission));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->compareData($this->data($permission)));
	}

	public function test_update_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAs($parent)
		                 ->patch($this->update($permission->id), $this->data($permission));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('permissions', $this->compareData($this->data($permission)));
	}

	public function test_update_guest(): void
	{
		$guest = Person::where('email', 'guest@kalinec.net')->first();
		$permission = SchoolPermission::find(1);
		$response = $this->actingAsGuest()
		                 ->patch($this->update($permission->id), $this->data($permission));
		$this->assertDatabaseMissing('permissions', $this->compareData($this->data($permission)));
	}

}
