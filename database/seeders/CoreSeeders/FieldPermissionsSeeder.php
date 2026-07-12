<?php

namespace Database\Seeders\CoreSeeders;

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
		$permissions = file_get_contents(database_path('data/field_permissions.json'));
		if ($permissions !== false)
		{

			$json = json_decode($permissions, true);
			$roleIdsByName = SchoolRoles::all()->pluck('id', 'name')->toArray();
			foreach ($json['rows'] as $row)
			{
				FieldPermission::create(
					[
						'viewer_role_id' => $roleIdsByName[$row['viewer_role']] ?? null,
						'target_role_id' => $roleIdsByName[$row['target_role']] ?? null,
						'context'        => $row['context'],
						'action'         => $row['action'],
						'field'          => $row['field'],
						'allow'          => $row['allow'],
					]);
			}
		}
	}
}
