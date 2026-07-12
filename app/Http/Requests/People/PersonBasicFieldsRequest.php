<?php

namespace App\Http\Requests\People;

use App\Classes\People\FieldAccessService;
use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
use App\Models\People\Person;
use Illuminate\Foundation\Http\FormRequest;

class PersonBasicFieldsRequest extends FormRequest
{
	protected array $matrix;
	protected FieldPermissionContext $context;
	protected array $targetRoleIds;
	protected FieldAccessService $accessService;

	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		$viewer = $this->user();
		$targetPerson = $this->route('person');

		return $viewer->can('edit', $targetPerson);
	}

	public function prepareForValidation()
	{
		$viewer = $this->user();
		/** @var Person $targetPerson */
		$targetPerson = $this->route('person');
		$this->accessService = app(FieldAccessService::class);
		$this->matrix = $this->accessService->permissionMatrixForPerson($viewer);
		$this->context = $this->accessService->determineContext($viewer, $targetPerson);
		$this->targetRoleIds = $targetPerson->roles->pluck('id')->toArray();
		$incomingData = $this->all();
		// Loop through submitted data and strip un-editable fields
		$fieldKeys = array_keys($incomingData);
		foreach ($fieldKeys as $fieldKey)
		{
			$correctKey = $fieldKey;
			if (str_starts_with($fieldKey, 'role_fields_'))
			{
				$correctKey = str_replace('role_fields_', 'role_fields.', $fieldKey);
				//$incomingData[$correctKey] = $incomingData[$fieldKey];
				//unset($incomingData[$fieldKey]);
			}
			// Use the exact same engine logic!
			$isAllowed = $this->accessService->evaluatePermissionDenyFirst(
				$this->matrix, $this->targetRoleIds, $this->context, FieldPermissionAction::EDIT, $correctKey
			);

			if (!$isAllowed)
			{
				// Strip the field before validation even runs!
				unset($incomingData[$fieldKey]);
			}
		}

		$this->replace($incomingData);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		$targetPerson = $this->route('person');
		$allRules =
			[
				'first' => 'nullable|string|max:255',
				'middle' => 'nullable|string|max:255',
				'last' => 'required|string|max:255',
				'email' => 'nullable|email|max:255',
				'nick' => 'nullable|string|max:255',
				'dob' => 'nullable|date',
			];
		foreach ($targetPerson->schoolRoles as $role)
		{
			if (empty($role->fields))
				continue;

			foreach ($role->fields as $field)
				$allRules['role_fields_' . $field->fieldId] = $field->validationArray();
		}
		$allowedRules = [];
		foreach ($allRules as $fieldId => $ruleSet)
		{
			$isAllowed = $this->accessService->evaluatePermissionDenyFirst(
				$this->matrix, $this->targetRoleIds, $this->context, FieldPermissionAction::EDIT, $fieldId
			);

			if ($isAllowed)
				$allowedRules[$fieldId] = $ruleSet;
		}

		return $allowedRules;
	}
}
