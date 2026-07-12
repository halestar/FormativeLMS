<?php

namespace App\Classes\People;

use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
use App\Models\People\FieldPermission;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class FieldAccessService
{
	protected array $contextCache = [];

	public function permissionMatrixForPerson(Person $target): array
	{
		$cacheTags =
			[
				'permission-matrix',
				'person- ' . $target->id,
			];
		return Cache::tags($cacheTags)->rememberForever('permission_matrix_' .
		                                                $target->id, function () use ($target)
		{
			if ($target->can('people.edit'))
				return ['global_override' => true];
			$matrix = ['global_override' => false];
			$roleIds = $target->roles->pluck('id')->toArray();
			$rules = FieldPermission::whereIn('viewer_role_id', $roleIds)->orWhereNull('viewer_role_id')->get();

			foreach ($rules as $rule)
			{
				$target = $rule->target_role_id;
				$context = $rule->context->value;
				$action = $rule->action->value;
				$field = $rule->field;
				$allow = $rule->allow;
				if ($context == FieldPermissionContext::SELF->value)
					$matrix[$context][$action][$field] = $allow;
				elseif (!isset($matrix[$target][$context][$action][$field]))
					$matrix[$target][$context][$action][$field] = $allow;
				else
					$matrix[$target][$context][$action][$field] = $matrix[$target][$context][$action][$field] || $allow;
			}
			return $matrix;
		});
	}

	public function determineContext(Person $viewer, Person $target): FieldPermissionContext
	{
		$cacheKey = "view_context_{$viewer->id}_{$target->id}";
		$cacheTags =
			[
				'permission-matrix',
				'person- ' . $target->id,
			];
		if (isset($this->contextCache[$cacheKey]))
			return $this->contextCache[$cacheKey];

		$context = Cache::tags($cacheTags)
		                ->remember($cacheKey, now()->addMinutes(60), function () use ($viewer, $target)
		                {
			                if ($viewer->id === $target->id)
				                return FieldPermissionContext::SELF;
			                if ($viewer->isParentOfPerson($target) || $target->isParentOfPerson($viewer))
				                return FieldPermissionContext::CHILD;

			                if ($viewer->isTeacher() && $target->isStudent())
			                {
				                $student = $target->student();
				                if ($student &&
				                    $viewer->currentClassSessions()
				                           ->whereHas('students', fn (Builder $query) => $query->where('student_id', $student->id))
				                           ->count() > 0)
				                {
					                return FieldPermissionContext::ROSTER;
				                }
			                }

			                return FieldPermissionContext::OTHER;
		                });
		$this->contextCache[$cacheKey] = $context;
		return $context;

	}

	public function evaluatePermission(array                  $matrix, array $targetRoleIds,
	                                   FieldPermissionContext $context,
	                                   FieldPermissionAction  $action, string $fieldKey): bool
	{
		if ($matrix['global_override'] ?? false)
			return true;
		if ($context === FieldPermissionContext::SELF)
			return $matrix[$context->value][$action->value][$fieldKey] ?? true;
		return array_any($targetRoleIds, fn ($roleId) => $matrix[$roleId][$context->value][$action->value][$fieldKey] ??
		                                                 true);
	}

	public function evaluatePermissionDenyFirst(array                  $matrix, array $targetRoleIds,
	                                            FieldPermissionContext $context,
	                                            FieldPermissionAction  $action, string $fieldKey): bool
	{
		if ($matrix['global_override'] ?? false)
			return true;
		if ($context === FieldPermissionContext::SELF)
			return $matrix[$context->value][$action->value][$fieldKey] ?? true;
		return array_any($targetRoleIds, fn ($roleId) => $matrix[$roleId][$context->value][$action->value][$fieldKey] ??
		                                                 false);
	}

}
