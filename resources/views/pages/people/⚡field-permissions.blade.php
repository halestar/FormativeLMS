<?php

use App\Classes\SessionSettings;
use App\Models\Utilities\SchoolRoles;
use App\Traits\FullPageComponent;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Enums\FieldPermissionContext;

new class extends Component
{
	use FullPageComponent;

	public string $selectedTab;

	#[Computed]
	public function tabs(): array
	{
		return
			[
				'student' =>
					[
						'title' => __('people.policies.view.viewing.students'),
						'target_roles' => [SchoolRoles::$STUDENT],
						'viewer_columns' =>
							[
								FieldPermissionContext::CHILD->value,
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$STUDENT,
								FieldPermissionContext::OTHER->value,
							],
					],
				'parent' =>
					[
						'title' => __('people.policies.view.viewing.parents'),
						'target_roles' => [SchoolRoles::$PARENT],
						'viewer_columns' =>
							[
								FieldPermissionContext::CHILD->value,
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$PARENT,
								FieldPermissionContext::OTHER->value,
							],
					],
				'employee' =>
					[
						'title' => __('people.policies.view.viewing.employees'),
						'target_roles' =>
							[
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$FACULTY,
								SchoolRoles::$STAFF,
								SchoolRoles::$COACH,
								SchoolRoles::$SUBSTITUTE,
							],
						'viewer_columns' =>
							[
								SchoolRoles::$EMPLOYEE,
								SchoolRoles::$PARENT,
								SchoolRoles::$STUDENT,
								FieldPermissionContext::OTHER->value,
							],
					],
			];
	}

	public function mount()
	{
		$this->authorize('has-permission', 'people.field.permissions');
		$this->breadcrumb =
			[
				__('people.fields.roles') => route('people.fields.roles'),
				__('system.menu.fields') => '#',
			];
		$this->selectedTab = SessionSettings::get('roles.fields.permissions', 'student');
	}

	public function setTab(string $tabName)
	{
		if (isset($this->tabs()[$tabName]))
		{
			$this->selectedTab = $tabName;
			SessionSettings::set('roles.fields.permissions', $this->selectedTab);
		}
	}
};
?>

<div class="container-xl">
    <div class="mb-3">
        <a href="{{ route('people.fields.roles') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>{{ __('common.back') ?? 'Back' }}
        </a>
    </div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                @foreach($this->tabs as $tabId => $tab)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link @if($tabId == $selectedTab) active @endif"
                                type="button"
                                @if($tabId == $selectedTab) aria-current="page" @endif
                                wire:click="setTab('{{ $tabId }}')"
                        >{{ $tab['title'] }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active"
                     role="tabpanel" tabindex="0"
                >
                    <div class="p-4">
                        <livewire:people.role-permissions :roles="$this->tabs[$selectedTab]['target_roles']"
                                                          wire:key="role-permissions-{{ $selectedTab }}"
                                                          :viewers="$this->tabs[$selectedTab]['viewer_columns']"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>