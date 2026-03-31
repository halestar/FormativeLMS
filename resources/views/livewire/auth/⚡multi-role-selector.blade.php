<?php

use App\Models\Utilities\SchoolRoles;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Modelable;
use Livewire\Component;

new class extends Component
{
	#[Modelable]
	public array $role_ids = [];
	#[Locked]
	public string $classes = "";
	#[Locked]
	public string $style = "";
	public Collection $roles;
	public int $selectedRoleId = 0;

	private function updateRoles()
	{
		$this->roles = SchoolRoles::whereNotIn('id', array_keys($this->role_ids))
			->excludeAdmin()->get();
    }

	public function mount()
	{
		$this->updateRoles();
    }

	public function addRole()
	{
		$role = $this->roles->first(fn($item) => $item->id == $this->selectedRoleId);
		if($role)
			$this->role_ids[$role->id] = $role->name;
		$this->updateRoles();
		$this->selectedRoleId = 0;
    }

	public function removeRole(int $role_id)
	{
		if(isset($this->role_ids[$role_id]))
			unset($this->role_ids[$role_id]);
		$this->updateRoles();
    }
};
?>

<div class="{{ $classes }}" style="{{ $style }}">
    <div class="input-group input-group-sm mb-2">
        <span class="input-group-text fw-semibold text-uppercase small">{{ __('settings.role.assign') }}</span>
        <select
                class="form-select form-select-sm"
                wire:model="selectedRoleId"
                wire:change="addRole(); $parent.set('changed', true)"
        >
            <option value="0">{{ __('people.roles.select') }}</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="alert alert-info py-2 px-2 mb-2">
        @if(count($role_ids))
            <div class="d-flex flex-wrap gap-1">
                @foreach($role_ids as $roleId => $role)
                    <span class="badge text-bg-primary d-inline-flex align-items-center gap-1 px-2 py-1 show-as-action">
                        <span class="small">{{ $role }}</span>
                        <button
                                type="button"
                                class="btn btn-link p-0 m-0 text-danger lh-1"
                                wire:click="removeRole({{ $roleId }}); $parent.set('changed', true)"
                                aria-label="{{ __('crud.remove') }}"
                        >
                            <i class="fa fa-times"></i>
                        </button>
                    </span>
                @endforeach
            </div>
        @else
            <small class="text-muted">{{ __('auth.roles.min') }}</small>
        @endif
    </div>
</div>
