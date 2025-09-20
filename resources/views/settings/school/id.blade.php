@inject('idSettings','App\Classes\Settings\IdSettings')
<div class="row">
    <div class="col">
        <form action="{{ route('settings.school.update.ids') }}" method="POST">
            @csrf
            @method('PATCH')
            <h3>{{ __('people.id.strategy') }}</h3>
            <div class="form-check">
                <input
                        class="form-check-input"
                        type="radio"
                        name="id_strategy"
                        id="id_strategy_global"
                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL }}"
                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL)
                />
                <label class="form-check-label" for="id_strategy_global">
                    {{ trans_choice('people.id.global', 1) }}
                </label>
            </div>
            <div class="alert alert-info">
                {{ __('people.id.global.help') }}
            </div>
            <div class="form-check">
                <input
                        class="form-check-input"
                        type="radio"
                        name="id_strategy"
                        id="id_strategy_roles"
                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES }}"
                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES)
                >
                <label class="form-check-label" for="id_strategy_roles">
                    {{ __('people.id.roles') }}
                </label>
            </div>
            <div class="alert alert-info">
                {{ __('people.id.roles.help') }}
            </div>

            <div class="form-check">
                <input
                        class="form-check-input"
                        type="radio"
                        name="id_strategy"
                        id="id_strategy_campuses"
                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES }}"
                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES)
                />
                <label class="form-check-label" for="id_strategy_campuses">
                    {{ __('people.id.campuses') }}
                </label>
            </div>
            <div class="alert alert-info">
                {{ __('people.id.campuses.help') }}
            </div>

            <div class="form-check">
                <input
                        class="form-check-input"
                        type="radio"
                        name="id_strategy"
                        id="id_strategy_both"
                        value="{{ \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH }}"
                        @checked($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH)
                />
                <label class="form-check-label" for="id_strategy_both">
                    {{ __('people.id.both') }}
                </label>
            </div>
            <div class="alert alert-info">
                {{ __('people.id.both.help') }}
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary col">{{ __('common.update') }}</button>
            </div>
        </form>
    </div>
    <div class="col">
        <h3 class="my-3">{{ trans_choice('people.id', 2) }}</h3>
        @if($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_GLOBAL)
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="border-bottom">{{ trans_choice('people.id.global',1) }}</h4>
                <a href="{{ route('people.school-ids.manage.global') }}" class="text-primary"><i
                            class="fa-solid fa-edit"></i></a>
            </div>
            @if($idSettings->getGlobalId())
                <div class="mb-3">{!! $idSettings->getGlobalId()->preview !!}</div>
            @else
                <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
            @endif
        @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_ROLES)
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="border-bottom">{{ __('people.id.student') }}</h4>
                <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::StudentRole()) }}"
                   class="text-primary"><i class="fa-solid fa-edit"></i></a>
            </div>
            @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::StudentRole())->preview)
                <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::StudentRole())->preview !!}</div>
            @else
                <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
            @endif

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="border-bottom">{{ __('people.id.parent') }}</h4>
                <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::ParentRole()) }}"
                   class="text-primary"><i class="fa-solid fa-edit"></i></a>
            </div>
            @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::ParentRole())->preview)
                <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::ParentRole())->preview !!}</div>
            @else
                <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
            @endif

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4 class="border-bottom">{{ __('people.id.employee') }}</h4>
                <a href="{{ route('people.school-ids.manage.role', \App\Models\Utilities\SchoolRoles::EmployeeRole()) }}"
                   class="text-primary"><i class="fa-solid fa-edit"></i></a>
            </div>
            @if($idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::EmployeeRole())->preview)
                <div class="mb-3">{!! $idSettings->getRoleId(\App\Models\Utilities\SchoolRoles::EmployeeRole())->preview !!}</div>
            @else
                <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
            @endif
        @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_CAMPUSES)
            @foreach(\App\Models\Locations\Campus::all() as $campus)
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="border-bottom">{{ __('people.id.campus', ['campus' => $campus->name]) }}</h4>
                    <a href="{{ route('people.school-ids.manage.campus', $campus) }}" class="text-primary"><i
                                class="fa-solid fa-edit"></i></a>
                </div>
                @if($idSettings->getCampusId($campus)->preview)
                    <div class="mb-3">{!! $idSettings->getCampusId($campus)->preview !!}</div>
                @else
                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                @endif
            @endforeach
        @elseif($idSettings->idStrategy == \App\Classes\Settings\IdSettings::ID_STRATEGY_BOTH)
            @foreach(\App\Models\Locations\Campus::all() as $campus)
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="border-bottom">{{ __('people.id.both.student', ['campus' => $campus->name]) }}</h4>
                    <a href="{{ route('people.school-ids.manage.both', ['role' => \App\Models\Utilities\SchoolRoles::StudentRole(), 'campus' => $campus]) }}"
                       class="text-primary"><i class="fa-solid fa-edit"></i></a>
                </div>
                @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::StudentRole(), $campus)->preview)
                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::StudentRole(), $campus)->preview !!}</div>
                @else
                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                @endif

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="border-bottom">{{ __('people.id.both.parent', ['campus' => $campus->name]) }}</h4>
                    <a href="{{ route('people.school-ids.manage.both', [\App\Models\Utilities\SchoolRoles::ParentRole(), $campus]) }}"
                       class="text-primary"><i class="fa-solid fa-edit"></i></a>
                </div>
                @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::ParentRole(), $campus)->preview)
                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::ParentRole(), $campus)->preview !!}</div>
                @else
                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                @endif
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="border-bottom">{{ __('people.id.both.employee', ['campus' => $campus->name]) }}</h4>
                    <a href="{{ route('people.school-ids.manage.both',[\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus]) }}"
                       class="text-primary"><i class="fa-solid fa-edit"></i></a>
                </div>
                @if($idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus)->preview)
                    <div class="mb-3">{!! $idSettings->getRoleCampusId(\App\Models\Utilities\SchoolRoles::EmployeeRole(), $campus)->preview !!}</div>
                @else
                    <div class="fs-5 mb-3">{{ __('people.id.no.preview') }}</div>
                @endif
            @endforeach
        @endif
    </div>
</div>