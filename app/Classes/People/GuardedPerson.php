<?php

namespace App\Classes\People;

use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
use App\Interfaces\Synthesizable;
use App\Models\People\Person;

readonly class GuardedPerson implements Synthesizable
{
	public Person $model;
	public array $matrix;
	public FieldPermissionContext $context;
	public array $targetRoleIds;
	public FieldAccessService $accessService;
	public int $viewerId;

	public function __construct(Person $model, Person $viewer)
	{
		$this->model = $model;
		$this->viewerId = $viewer->id;
		$this->accessService = app(FieldAccessService::class);

		// Pre-load security data once per model
		$this->matrix = $this->accessService->permissionMatrixForPerson($viewer);
		$this->context = $this->accessService->determineContext($viewer, $model);
		$this->targetRoleIds = $model->schoolRoles->pluck('id')->toArray();
	}

	public function toArray(): array
	{
		return
			[
				'model_id'  => $this->model->id,
				'viewer_id' => $this->viewerId,
			];
	}

	public static function hydrate(array $data): static
	{
		return new static(Person::find($data['model_id']), Person::find($data['viewer_id']));
	}


	/**
	 * Intercept all property accesses: $guardedPerson->dob
	 */
	public function __get($key)
	{
		if (!$this->canView($key))
			return null;

		if ($key == "role_fields")
			return new GuardedRoleFields($this->model->role_fields, $this);
		return $this->model->{$key};
	}

	/**
	 * Pass unhandled method calls (like $guarded->formatName()) back to the model
	 */
	public function __call($method, $args)
	{
		return $this->model->$method(...$args);
	}

	public function canEdit(string $key): bool
	{
		return $this->accessService->evaluatePermissionDenyFirst(
			$this->matrix, $this->targetRoleIds, $this->context, FieldPermissionAction::EDIT, $key
		);
	}

	public function canView(string $key): bool
	{
		return $this->accessService->evaluatePermissionDenyFirst(
			$this->matrix, $this->targetRoleIds, $this->context, FieldPermissionAction::VIEW, $key
		);
	}

	public function __toString(): string
	{
		return $this->model->__toString();
	}
}
