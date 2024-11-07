@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="row profile-head-row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img
                        class="img-fluid img-thumbnail"
                        src="{{ $campus->img }}"
                        alt="{{ __('locations.campus.img') }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head d-flex align-items-start flex-column h-100">
                    <h5>
                        {{ $campus->name }}
                        ({{ $campus->abbr }})
                    </h5>
                    <h6 class="d-flex flex-column">
                        @if($campus->title)
                        <p class="lead mb-2">"{{ $campus->title }}"</p>
                        @endif
                        <div>
                            <strong class="me-2">{{ trans_choice('crud.level',1) }}:</strong>
                            {{ $campus->levels->pluck('name')->join(', ') }}
                        </div>
                        <div class="border rounded p-2 align-self-start" style="background-color: {{ $campus->color_pri }};" id="icon-container">
                            <div class="img-mini" style="color: {{ $campus->color_sec }};">{!! $campus->icon !!}</div>
                        </div>
                    </h6>
                    <ul class="nav nav-tabs mt-auto" id="profile-tab" role="tablist">
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                id="tab-contact"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-pane-contact"
                                href="#tab-pane-contact"
                                role="tab"
                                aria-controls="#tab-pane-contact"
                                aria-selected="true"
                            >{{ __('locations.campus.information.contact') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a
                    type="button"
                    class="btn btn-secondary profile-edit-btn"
                    href="{{ route('locations.campuses.edit', ['campus' => $campus->id]) }}"
                >{{ __('locations.campus.edit') }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>Link Group 2</p>
                    <a href="">Link 1</a><br/>
                    <a href="">Link 2</a><br/>
                    <a href="">Link 3</a><br/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="tab-content profile-tab" id="profile-tab-content">
                    <div
                        class="tab-pane fade show active"
                        id="tab-pane-contact" role="tabpanel" aria-labelledby="tab-contact" tabindex="0"
                    >
                            <ul class="list-group">
                                <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                    <div class="d-flex justify-content-between align-items-top">
                                        <label>
                                            {{ __('locations.campus.address') }}
                                        </label>
                                        <span>{!! nl2br($campus->prettyAddress()) !!}</span>
                                    </div>
                                </li>
                                @if($campus->phones()->count() > 0)
                                    @foreach($campus->phones as $phone)
                                            <li class="list-group list-group-flush border-bottom mb-2 pb-1">
                                                <div class="d-flex justify-content-between align-items-top">
                                                    <label>
                                                        {{ $phone->personal->label }}
                                                    </label>
                                                    <span>{{ $phone->pretty_phone }}</span>
                                                </div>
                                            </li>
                                    @endforeach
                                @endif
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
