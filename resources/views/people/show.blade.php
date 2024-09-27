@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        @if($self->canViewField($self->viewableField('portrait_url'),$person) && $person->portrait_url)
                            src="{{ $person->portrait_url }}"
                        @else
                            src='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>'
                        @endif
                        alt="{{ __('people.profile.image') }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $person->name }}
                    </h5>
                    <h6>
                        {{ $person->roles->pluck('name')->join(', ') }}
                    </h6>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
                        @foreach(\App\Models\CRUD\ViewableGroup::viewable()->get() as $tab)
                        <li class="nav-item">
                            <a
                                class="nav-link @if($loop->first) active @endif"
                                id="tab-{{ $tab->id }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-{{ $tab->id }}"
                                href="#tab-pane-{{ $tab->id }}"
                                role="tab"
                                aria-controls="#tab-pane-{{ $tab->id }}"
                                aria-selected="true"
                            >{{ $tab->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @can('edit', $person)
            <div class="col-md-2">
                <a type="button" class="btn btn-secondary profile-edit-btn" href="{{ route('people.edit', ['person' => $person->id]) }}">{{ __('people.profile.edit') }}</a>
            </div>
            @endcan
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    @if($isSelf)
                    <p>{{ __('people.profile.links.groups.settings') }}</p>
                    <a data-bs-toggle="offcanvas" href="#privacy-permissions" aria-controls="privacy-permissions">{{ __('people.profile.links.settings.personal_view_policy') }}</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                    @endif
                    <p>Link Group 2</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    @foreach(\App\Models\CRUD\ViewableGroup::viewable()->get() as $tab)
                    <div
                        class="tab-pane fade show @if($loop->first) active @endif"
                        id="tab-pane-{{ $tab->id }}" role="tabpanel" aria-labelledby="tab-{{ $tab->id }}" tabindex="{{ $tab->order }}"
                    >
                        <ul class="list-group">
                            @foreach($tab->fields as $field)
                                @if($self->canViewField($field, $person))
                                    <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>{{ $field->name }}</label>
                                            <span>{{ $field->fieldValue($person) }}</span>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($isSelf)
    <div class="offcanvas offcanvas-start" tabindex="-1" id="privacy-permissions" aria-labelledby="privacy-permissions-label">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="privacy-permissions-label">{{ __('people.profile.links.settings.personal_view_policy') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('common.close') }}"></button>
        </div>
        <div class="offcanvas-body">
            <livewire:self-viewing-permissions />
        </div>
    </div>
    @endif
@endsection
