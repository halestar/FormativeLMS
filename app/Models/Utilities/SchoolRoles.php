<?php

namespace App\Models\Utilities;

use App\Classes\RoleField;
use App\Models\People\FieldPermission;
use App\Models\People\RoleFields;
use halestar\LaravelDropInCms\Models\Scopes\OrderByNameScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role;

#[ScopedBy(OrderByNameScope::class)]
class SchoolRoles extends Role
{
	public static string $ADMIN = "Super Admin";
	public static string $EMPLOYEE = "Employee";
	public static string $STUDENT = "Student";
	public static string $FACULTY = "Faculty";
	public static string $STAFF = "Staff";
	public static string $COACH = "Coach";
	public static string $PARENT = "Parent";
	public static string $OLD_STUDENT = "Old Student";
	public static string $OLD_FACULTY = "Old Faculty";
	public static string $OLD_STAFF = "Old Staff";
	public static string $OLD_COACH = "Old Coach";
	public static string $OLD_PARENT = "Old Parent";
	public static array $baseRolePermissions =
		[
			"Super Admin" => [],
			"Student" => [],
			"Employee" => [],
			"Faculty" => [],
			"Staff" => ['school.tracker', 'subjects.skills'],
			"Coach" => ['school.tracker'],
			"Parent" => [],
			"Old Student" => [],
			"Old Faculty" => [],
			"Old Staff" => [],
			"Old Coach" => [],
			"Old Parent" => [],
		];
	
	public static function getDefaultPermissions(string $role): array
	{
		return SchoolRoles::$baseRolePermissions[$role] ?? [];
	}
	
	public static function EmployeeRole(): SchoolRoles
	{
		return SchoolRoles::where('name', '=', self::$EMPLOYEE)
		                  ->first();
	}
	
	public static function StudentRole(): SchoolRoles
	{
		return SchoolRoles::where('name', '=', self::$STUDENT)
		                  ->first();
	}
	
	public static function ParentRole(): SchoolRoles
	{
		return SchoolRoles::where('name', '=', self::$PARENT)
		                  ->first();
	}
	
	public static function setDefaultPermissions(array $defaultPermissions): void
	{
		self::$baseRolePermissions = $defaultPermissions;
	}
	
	protected static function booted(): void
	{
		static::addGlobalScope('base_name_order', function(Builder $builder)
		{
			$builder->orderBy('base_role')
			        ->orderBy('name');
		});
	}
	
	public function scopeBaseRoles(Builder $query): void
	{
		$query->where('base_role', true);
	}
	
	public function scopeNormalRoles(Builder $query): void
	{
		$query->where('base_role', false);
	}
	
	public function scopeExcludeAdmin(Builder $query): void
	{
		$query->where('name', '<>', SchoolRoles::$ADMIN);
	}
	
	public function syncFieldPermissions(): void
	{
		$fields = $this->fields;
		$fieldIds = [];
		foreach($fields as $field)
		{
			//we will check if we have permissions for this field.
			if(!FieldPermission::where('field', $field->fieldId)
			                   ->where('role_id', $this->id)
			                   ->exists())
			{
				//we don't have an entry, so we create one.
				FieldPermission::create(
					[
						'field' => $field->fieldId,
						'role_id' => $this->id,
					]);
			}
			// and we save the field name
			$fieldIds[] = $field->fieldId;
		}
		// at this point all the permissions are there, so we prune any extraneous ones.
		FieldPermission::where('role_id', $this->id)
		               ->whereNotIn('field', $fieldIds)
		               ->delete();
	}
	
	protected function casts(): array
	{
		return [
			'base_role' => 'boolean',
		];
	}
	
	protected function fields(): Attribute
	{
		if($this->pivot && $this->pivot->field_values)
			$fieldValues = json_decode($this->pivot->field_values, true);
		else
			$fieldValues = null;
		return Attribute::make(
			get: function(?string $value) use ($fieldValues)
			{
				if(!$value)
					return [];
				$fields = [];
				$value = json_decode($value, true);
				foreach($value as $field)
				{
					if($fieldValues)
						$field['fieldValue'] = $fieldValues[$field['fieldId']] ?? null;
					$field['roleId'] = $this->id;
					$fields[$field['fieldId']] = new RoleField($field);
				}
				return $fields;
			},
			set: function(?array $value)
			{
				if(!$value)
					return json_encode([]);
				$fields = [];
				foreach($value as $field)
					$fields[$field->fieldId] = $field->toArray();
				return json_encode($fields);
			}
		);
	}
}
