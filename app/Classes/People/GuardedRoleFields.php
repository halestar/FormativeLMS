<?php

namespace App\Classes\People;

use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;

class GuardedRoleFields
{
	public readonly RoleFields $model;
	public readonly array $matrix;
	public readonly FieldPermissionContext $context;
	public readonly array $targetRoleIds;
	public readonly FieldAccessService $accessService;

	public function __construct(RoleFields $model, GuardedPerson $person)
	{
		$this->model = $model;
		$this->accessService = app(FieldAccessService::class);

		// Pre-load security data once per model
		$this->matrix = $person->matrix;
		$this->context = $person->context;
		$this->targetRoleIds = $person->targetRoleIds;
	}

	public function __get($key)
	{
		$permissionField = "role_fields." . $key;
		$isAllowed = $this->accessService->evaluatePermission(
			$this->matrix, $this->targetRoleIds, $this->context, FieldPermissionAction::VIEW, $permissionField
		);

		if (!$isAllowed)
			return null;

		return $this->model->{$key};
	}

	/**
	 * Pass unhandled method calls (like $guarded->formatName()) back to the model
	 */
	public function __call($method, $args)
	{
		return $this->model->$method(...$args);
	}

}
