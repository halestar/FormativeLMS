<?php

namespace App\Rules;

use App\Models\People\RoleFields;
use App\Models\Utilities\SchoolRoles;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueJsonName implements ValidationRule
{
	protected SchoolRoles $role;
	
	public function __construct(SchoolRoles $role)
	{
		$this->role = $role;
	}
	
	
	/**
	 * Run the validation rule.
	 *
	 * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
	 */
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$existingFields = $this->role->fields;
		foreach($existingFields as $field)
			if($field->fieldName == $value)
				$fail(
					'The name ' . $value . ' is already in use as another field name.');
	}
}
