<?php

namespace Tests\Feature\Roles;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EditTest extends TestCase
{

	use DatabaseTransactions;

	protected function index(): string
	{
		return route('settings.roles.index');
	}

	protected function update($id): string
	{
		return route('settings.roles.update', $id);
	}

	protected function data(SchoolRoles $role): array
	{
		return
			[
				'name'        => $role->name . "_new",
				'permissions' => $role->permissions->pluck('id')->push(15)->toArray(),
			];
	}

	protected function compareData(array $data): array
	{
		return
			[
				'name' => $data['name'],
			];
	}

	public function test_update_admin(): void
	{
		$admin = Person::where('email', 'fablms@kalinec.net')->first();
		$role = SchoolRoles::find(15);
		$response = $this->actingAs($admin)
		                 ->patch($this->update($role->id), $this->data($role));
		$response->assertRedirect($this->index());
		$this->assertDatabaseHas('roles', ['name' => $this->data($role)['name']]);
		$model = SchoolRoles::find($role->id);
		$this->assertInstanceOf(SchoolRoles::class, $model);
		$this->assertInstanceOf(BelongsToMany::class, $model->permissions());
		foreach ($this->data($role)['permissions'] as $permission_id)
			$this->assertTrue($model->permissions()->where('id', $permission_id)->exists());
	}

	public function test_update_staff(): void
	{
		$staff = Person::where('email', 'staff@kalinec.net')->first();
		$role = SchoolRoles::find(15);
		$response = $this->actingAs($staff)
		                 ->patch($this->update($role->id), $this->data($role));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', $this->compareData($this->data($role)));
	}

	public function test_update_faculty(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$role = SchoolRoles::find(15);
		$response = $this->actingAs($faculty)
		                 ->patch($this->update($role->id), $this->data($role));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', $this->compareData($this->data($role)));
	}

	public function test_update_student(): void
	{
		$student = Person::where('email', 'student@kalinec.net')->first();
		$role = SchoolRoles::find(15);
		$response = $this->actingAs($student)
		                 ->patch($this->update($role->id), $this->data($role));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', $this->compareData($this->data($role)));
	}

	public function test_update_parent(): void
	{
		$parent = Person::where('email', 'parent@kalinec.net')->first();
		$role = SchoolRoles::find(15);
		$response = $this->actingAs($parent)
		                 ->patch($this->update($role->id), $this->data($role));
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('roles', $this->compareData($this->data($role)));
	}

}
