<nav class="navbar navbar-expand-md bg-primary" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/fablms-512.png', true) }}" alt="FABLMS" width="32" height="32">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="peopleAdminDD" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('system.menu.people') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="schoolAdminDD">
                            <a class="dropdown-item" href="{{ route('people.school-ids.show') }}">
                                {{ __('people.id.mine') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('people.index') }}">
                                {{ __('system.menu.school.directory') }}
                            </a>
                        </div>
                    </li>
                    @canany(['locations.campuses', 'locations.years', 'locations.buildings', 'subjects.subjects',
                        'subjects.courses', 'subjects.classes', 'school.tracker.admin'])
                        <li class="nav-item dropdown">
                            <a id="schoolAdminDD" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('system.menu.school.administration') }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="schoolAdminDD">
                                @can('locations.campuses')
                                    <a class="dropdown-item" href="{{ route('locations.campuses.index') }}">
                                        {{ __('system.menu.campuses') }}
                                    </a>
                                @endcan
                                @can('locations.years')
                                    <a class="dropdown-item" href="{{ route('locations.years.index') }}">
                                        {{ __('system.menu.years') }}
                                    </a>
                                @endcan
                                @can('locations.buildings')
                                    <a class="dropdown-item" href="{{ route('locations.buildings.index') }}">
                                        {{ __('system.menu.rooms') }}
                                    </a>
                                @endcan
                                @can('subjects.subjects')
                                    <a class="dropdown-item" href="{{ route('subjects.subjects.index') }}">
                                        {{ trans_choice('subjects.subject',2) }}
                                    </a>
                                @endcan
                                @can('subjects.courses')
                                    <a class="dropdown-item" href="{{ route('subjects.courses.index') }}">
                                        {{ trans_choice('subjects.course',2) }}
                                    </a>
                                @endcan
                                @can('subjects.classes')
                                    <a class="dropdown-item" href="{{ route('subjects.classes.index') }}">
                                        {{ trans_choice('subjects.class',2) }}
                                    </a>
                                @endcan
                                @can('school.tracker.admin')
                                    <a class="dropdown-item" href="{{ route('subjects.student-tracker.index') }}">
                                        {{ __('school.student.tracking') }}
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endcanany
                    @canany(['classes.enrollment','subjects.skills'])
                        <li class="nav-item dropdown">
                            <a id="classManagementDD" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('system.menu.classes') }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="classManagementDD">
                                @can('classes.enrollment')
                                    <a class="dropdown-item" href="{{ route('subjects.enrollment.general') }}">
                                        {{ __('system.menu.classes.enrollment.general') }}
                                    </a>
                                @endcan
                                @can('subjects.skills')
                                    <a class="dropdown-item" href="{{ route('subjects.skills.index') }}">
                                        {{ trans_choice('subjects.skills', 2) }}
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endcanany
                    @hasanyrole(\App\Models\Utilities\SchoolRoles::$FACULTY . "|" . \App\Models\Utilities\SchoolRoles::$OLD_FACULTY)
                    <li class="nav-item dropdown">
                        <a id="learningManagementDD" class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('system.menu.teaching') }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="learningManagementDD">
                            <a class="dropdown-item" href="{{ route('learning.criteria') }}">
                                {{ __('system.menu.criteria') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('learning.ld.index') }}">
                                {{ trans_choice('learning.demonstrations', 2) }}
                            </a>
                        </div>
                    </li>
                    @endhasanyrole
                    @canany(['crud', 'cms', 'people.roles.fields', 'people.field.permissions'])
                        <li class="nav-item dropdown">
                            <a id="adminDD" class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ __('system.menu.admin') }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDD">
                                @can('crud')
                                    <a class="dropdown-item" href="{{ route('settings.system.tables') }}">
                                        {{ __('system.menu.system.tables') }}
                                    </a>
                                @endcan
                                @can('cms')
                                    <a class="dropdown-item" href="/cms">
                                        {{ __('system.menu.cms') }}
                                    </a>
                                @endcan
                                @can('people.roles.fields')
                                    <a class="dropdown-item" href="{{ route('people.roles.fields') }}">
                                        {{ __('people.fields.roles') }}
                                    </a>
                                @endcan
                                @can('people.field.permissions')
                                    <a class="dropdown-item" href="{{ route('people.fields.permissions') }}">
                                        {{ __('system.menu.fields') }}
                                    </a>
                                @endcan
                                @can('school')
                                    <a class="dropdown-item" href="{{ route('settings.school') }}">
                                        {{ __('system.menu.school.settings') }}
                                    </a>
                                @endcan
                                @can('settings.integrators')
                                    <a class="dropdown-item"
                                       href="{{ route(\App\Models\Integrations\Integrator::INTEGRATOR_ACTION_PREFIX . "index") }}">
                                        {{ __('system.menu.integrators') }}
                                    </a>
                                @endcan
                                @can('school.emails')
                                    <a class="dropdown-item" href="{{ route('settings.school.emails') }}">
                                        {{ __('system.menu.school.emails') }}
                                    </a>
                                @endcan
                            </div>
                        </li>
                    @endcanany
                    <li class="nav-item dropdown">
                        <a id="userDD" class="nav-link dropdown-toggle position-relative" href="#" role="button"
                           data-bs-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                            @impersonating()
                            <span class="badge text-bg-warning rounded-pill">I</span>
                            @endImpersonating
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDD">
                            <a class="dropdown-item"
                               href="{{ route('people.show', ['person' => Auth::user()->school_id]) }}">
                                {{ __('people.profile.mine') }}
                            </a>
                            @can('settings.permissions.view')
                                <a class="dropdown-item" href="{{ route('settings.permissions.index') }}">
                                    {{ __('settings.permissions') }}
                                </a>
                            @endcan
                            @can('settings.roles.view')
                                <a class="dropdown-item" href="{{ route('settings.roles.index') }}">
                                    {{ __('settings.roles') }}
                                </a>
                            @endcan
                            @impersonating()
                            <a class="dropdown-item" href="{{ route('unimpersonate') }}">
                                {{ __('people.impersonate.leave') }}
                            </a>
                            @else
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                                @endImpersonating
                        </div>
                    </li>
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item">
                        <button type="button" class="btn btn-success rounded rounded-5 btn-sm" data-bs-toggle="modal"
                                data-bs-target="#search-modal"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </li>
                    <li class="ms-3 nav-item">
                        <livewire:message-notifier/>
                    </li>
                    <li class="nav-item dropdown ms-3  @if(Auth::user()->alertNotifications()->count() == 0) d-none @endif"
                        id="notification-menu">
                        <a id="user-notifications" class="nav-link dropdown-toggle p-0" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <i class="fa-solid fa-bell fs-1 text-bright-alert"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="user-notifications"
                             id="notifications-dropdown-container">
                            @foreach(Auth::user()->alertNotifications as $notification)
                                @continue($notification->type == \App\Notifications\NewClassMessageNotification::class)
                                <x-notification :notification="$notification"/>
                            @endforeach
                        </div>
                    </li>
                @endauth

                <li class="nav-item">
                    <x-utilities.language-switcher/>
                </li>
            </ul>
        </div>
    </div>
</nav>
