<?php

use App\Classes\People\FieldRegistry;
use App\Enums\FieldPermissionAction;
use App\Enums\FieldPermissionContext;
use App\Models\People\FieldPermission;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

new class extends Component
{
	public array $roles;
	public array $viewers;
	public array $fields;
	public array $permissions;

	public function mount(array $roles, array $viewers, FieldRegistry $registry)
	{
		$this->roles = $roles;
		$this->viewers = $viewers;
		array_unshift($this->viewers, FieldPermissionContext::SELF->value);
		$registryFields = $registry->forRoles($this->roles);
		$this->fields = [];
		$this->permissions = [];
		foreach ($registryFields as $fieldId => $fieldName)
		{
			$fieldKey = str_replace(".", "__", $fieldId);
			$this->fields[$fieldKey] = $fieldName;
			foreach ($this->viewers as $viewer)
			{
				$this->permissions[$fieldKey][$viewer][FieldPermissionAction::VIEW->value] = false;
				$this->permissions[$fieldKey][$viewer][FieldPermissionAction::EDIT->value] = false;
			}
		}

		$roles = SchoolRoles::all();
		$roleIdsByName = $roles->pluck('id', 'name')->toArray();
		$roleNamesById = $roles->pluck('name', 'id')->toArray();

		$targetRoleIds = [];
		foreach ($this->roles as $roleName)
		{
			if (isset($roleIdsByName[$roleName]))
				$targetRoleIds[] = $roleIdsByName[$roleName];
		}

		$existingRules = FieldPermission::whereIn('target_role_id', $targetRoleIds)
		                                ->orWhere(function (Builder $query) use ($targetRoleIds)
		                                {
			                                $query->where('context', FieldPermissionContext::SELF)
			                                      ->whereIn('viewer_role_id', $targetRoleIds);
		                                })->get();
		foreach ($existingRules as $rule)
		{
			if (!$rule->allow)
				continue;

			$fieldKey = str_replace(".", "__", $rule->field);
			$action = $rule->action->value;
			$colKey = $this->getColKeyFromRule($rule, $roleNamesById);
			if ($colKey && in_array($colKey, $this->viewers))
				$this->permissions[$fieldKey][$colKey][$action] = true;
		}
	}

	private function getColKeyFromRule(FieldPermission $rule, $roleNamesById): ?string
	{
		if ($rule->context != FieldPermissionContext::OTHER)
			return $rule->context->value;

		if ($rule->viewer_role_id === null)
			return FieldPermissionContext::OTHER->value;
		$viewerName = $roleNamesById[$rule->viewer_role_id] ?? null;

		if ($viewerName == SchoolRoles::$STUDENT) return SchoolRoles::$STUDENT;
		if ($viewerName == SchoolRoles::$PARENT) return SchoolRoles::$PARENT;

		// Group all staff roles back into the single 'Employee' column
		if (in_array($viewerName, [
			SchoolRoles::$EMPLOYEE, SchoolRoles::$FACULTY, SchoolRoles::$STAFF, SchoolRoles::$COACH,
			SchoolRoles::$SUBSTITUTE
		]))
		{
			return SchoolRoles::$EMPLOYEE;
		}
		return null;
	}

	public function updatePermissions()
	{
		$columnTranslator =
			[
				FieldPermissionContext::SELF->value =>
					[
						'context' => FieldPermissionContext::SELF,
						'viewers' => 'SAME_AS_TARGET'
					],
				FieldPermissionContext::CHILD->value =>
					[
						'context' => FieldPermissionContext::CHILD,
						'viewers' => [SchoolRoles::$PARENT],
					],
				FieldPermissionContext::ROSTER->value =>
					[
						'context' => FieldPermissionContext::ROSTER,
						'viewers' =>
							[
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$FACULTY,
								SchoolRoles::$COACH,
							],
					],
				FieldPermissionContext::OTHER->value =>
					[
						'context' => FieldPermissionContext::OTHER,
						'viewers' => [FieldPermissionContext::OTHER->value]
					],
				SchoolRoles::$STUDENT =>
					[
						'context' => FieldPermissionContext::OTHER,
						'viewers' => [SchoolRoles::$STUDENT],
					],
				SchoolRoles::$PARENT =>
					[
						'context' => FieldPermissionContext::OTHER,
						'viewers' => [SchoolRoles::$PARENT],
					],
				SchoolRoles::$EMPLOYEE =>
					[
						'context' => FieldPermissionContext::OTHER,
						'viewers' =>
							[
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$FACULTY,
								SchoolRoles::$STAFF,
								SchoolRoles::$COACH,
								SchoolRoles::$SUBSTITUTE,
							],
					],
			];

		$roleIdsByName = SchoolRoles::all()->pluck('id', 'name')->toArray();

		DB::beginTransaction();
		foreach ($this->permissions as $fieldKey => $columns)
		{
			$fieldId = str_replace("__", ".", $fieldKey);
			foreach ($columns as $colKey => $action)
			{

				if (!isset($columnTranslator[$colKey])) continue;

				$translation = $columnTranslator[$colKey];
				$context = $translation['context'];
				$allowView = $action[FieldPermissionAction::VIEW->value];
				$allowEdit = $action[FieldPermissionAction::EDIT->value] ?? false;
				foreach ($this->roles as $role)
				{
					if (!isset($roleIdsByName[$role])) continue;
					$targetRoleId = $roleIdsByName[$role];

					$viewerRoleNames = ($translation['viewers'] === 'SAME_AS_TARGET')
						? [$role]
						: $translation['viewers'];
					foreach ($viewerRoleNames as $viewerName)
					{
						if (!isset($roleIdsByName[$viewerName]))
							$viewerRoleId = null;
						else
							$viewerRoleId = $roleIdsByName[$viewerName];
						$finalTargetId = ($context == FieldPermissionContext::SELF) ? null : $targetRoleId;

						FieldPermission::updateOrCreate(
							[
								'viewer_role_id' => $viewerRoleId,
								'target_role_id' => $finalTargetId,
								'context' => $context,
								'action' => FieldPermissionAction::VIEW,
								'field' => $fieldId
							],
							['allow' => $allowView]
						);

						if ($context == FieldPermissionContext::SELF)
						{
							FieldPermission::updateOrCreate(
								[
									'viewer_role_id' => $viewerRoleId,
									'target_role_id' => $finalTargetId,
									'context' => $context,
									'action' => FieldPermissionAction::EDIT,
									'field' => $fieldId
								],
								['allow' => $allowEdit]
							);
						}

					}
				}
			}
		}
		DB::commit();
		Cache::tags('permission-matrix')->flush();
		$this->js('new LmsToast("' . __('people.policies.view.updated') . '","' . __('people.policies.view.updated') .
		          '")');
	}
}
?>
<div>
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary" wire:click="updatePermissions">
            <i class="fa-solid fa-save me-2"></i>{{ __('people.policies.view.viewable_policy.update') }}
        </button>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover card-table">
                <thead class="table-light">
                <tr>
                    <th>{{ __('people.roles.field.name') }}</th>
                    @foreach($viewers as $viewer)
                        <th class="text-center">
                            @switch($viewer)
                                @case(\App\Enums\FieldPermissionContext::SELF->value)
                                    {{ __('people.policies.view.viewer.self') }}
                                    @break
                                @case(\App\Enums\FieldPermissionContext::CHILD->value)
                                    {{ __('people.policies.view.viewer.child') }}
                                    @break
                                @case(\App\Enums\FieldPermissionContext::ROSTER->value)
                                    {{ __('people.policies.view.viewer.roster') }}
                                    @break
                                @case(\App\Models\Utilities\SchoolRoles::$EMPLOYEE)
                                    {{ __('people.policies.view.viewer.employees') }}
                                    @break
                                @case(\App\Models\Utilities\SchoolRoles::$STUDENT)
                                    {{ __('people.policies.view.viewer.students') }}
                                    @break
                                @case(\App\Models\Utilities\SchoolRoles::$PARENT)
                                    {{ __('people.policies.view.viewer.parents') }}
                                    @break
                                @default
                                    {{ __('people.policies.view.viewer.community') }}
                                    @break
                            @endswitch
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($fields as $fieldId => $fieldName)
                    <tr>
                        <td class="fw-bold">{{ $fieldName }}</td>
                        @foreach($viewers as $viewer)
                            <td class="text-center">
                                <div class="d-flex flex-row justify-content-center align-items-center gap-3">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               role="switch"
                                               id="view-permission-{{ $fieldId }}-{{ $viewer }}"
                                               wire:model="permissions.{{ $fieldId }}.{{$viewer}}.view"
                                        />
                                        <label class="form-check-label text-muted small"
                                               for="view-permission-{{ $fieldId }}-{{ $viewer }}">
                                            <i class="fa-regular fa-eye me-1"></i>{{ __('common.view') }}
                                        </label>
                                    </div>
                                    @if($viewer == \App\Enums\FieldPermissionContext::SELF->value)
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   role="switch"
                                                   id="edit-permission-{{ $fieldId }}-{{ $viewer }}"
                                                   wire:model="permissions.{{ $fieldId }}.{{$viewer}}.edit"
                                            />
                                            <label class="form-check-label text-muted small"
                                                   for="edit-permission-{{ $fieldId }}-{{ $viewer }}">
                                                <i class="fa-solid fa-pencil me-1"></i>{{ __('common.edit') }}
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>