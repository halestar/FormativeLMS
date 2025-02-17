@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3" id="add-header">
            <h2>{{ trans_choice('locations.campus',2) }}</h2>
            <button
                class="btn btn-primary"
                onclick="$('#add-container,#add-header').toggleClass('d-none')"
                type="button"
            >
                <i class="fa fa-plus pe-1 me-1 border-end"></i>
                {{ __('locations.campus.add') }}
            </button>
        </div>
        <div class="card mb-2 text-bg-light d-none" id="add-container">
            <form action="{{ route('locations.campuses.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <label for="name" class="form-label">{{ __('locations.campus.name') }}</label>
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                id="name"
                            />
                            <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                        </div>
                        <div class="col-2">
                            <label for="abbr" class="form-label">{{ __('locations.campus.abbr') }}</label>
                            <input type="text" class="form-control" name="abbr" id="abbr"/>
                        </div>
                        <div class="col-2 align-self-end text-center">
                            <button type="submit" class="btn btn-primary">{{ __('locations.campus.add') }}</button>
                            <button
                                type="button"
                                onclick="$('#add-container,#add-header').toggleClass('d-none')"
                                class="btn btn-secondary"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @foreach(\App\Models\Locations\Campus::all() as $campus)
            <div class="card mb-3">
                <h3
                    class="card-header d-flex justify-content-between align-items-center"
                    style="background-color: {{ $campus->color_pri }} !important; color: {{ $campus->color_sec }} !important;"
                >
                    <span class="campus-header">{{ $campus->name }} ({{ $campus->abbr }})</span>
                    {!! $campus->iconHtml() !!}
                </h3>
                <div
                    class="card-body campus-back-img"
                    style="background-image: url('{{ $campus->img }}');"
                >
                    <div class="row p-3 rounded text-black" style="background-color: rgba(255, 255, 255, 0.6);">
                        <div class="col-4">
                            <h5>{{ __('locations.campus.information.basic') }}</h5>
                            @if($campus->title)
                                <p class="lead mb-0">"{{ $campus->title }}"</p>
                            @endif
                            @foreach($campus->addresses as $address)
                                <address>
                                    {!! nl2br($address->pretty_address) !!}
                                </address>
                            @endforeach
                            @if($campus->phones()->count() > 0)
                                <address>
                                    @foreach($campus->phones as $phone)
                                        <strong>
                                            @if($phone->personal->primary){{ __('addresses.primary') }}@endif
                                            {{ $phone->personal->label }}
                                            {{ __('phones.phone') }}:
                                        </strong>
                                        {{ $phone->pretty_phone }}
                                        @if(!$loop->last)
                                            <br/>
                                        @endif
                                    @endforeach
                                </address>
                            @endif
                        </div>
                        <div class="col-4">
                            <h5>{{ __('locations.campus.information.statistics') }}</h5>
                            <div class="d-flex flex-column">
                                <span class="statistic">
                                    {{ $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$STAFF)->count() }}
                                    {{ trans_choice('people.staff', $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$STAFF)->count()) }}
                                </span>
                                <span class="statistic">
                                    {{ $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$FACULTY)->count() }}
                                    {{ trans_choice('people.faculty', $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$FACULTY)->count()) }}
                                </span>
                                <span class="statistic">
                                    {{ $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$COACH)->count() }}
                                    {{ trans_choice('people.coach', $campus->employeesByRole(\App\Models\Utilities\SchoolRoles::$COACH)->count()) }}
                                </span>
                                <span class="statistic">
                                    {{ $campus->students()->count() }}
                                    {{ trans_choice('people.student',$campus->students()->count()) }}
                                </span>
                                <span class="statistic">
                                    {{ $campus->parents()->count() }}
                                    {{ trans_choice('people.parent', $campus->parents()->count()) }}
                                </span>
                                <span class="statistic">{{ $campus->rooms()->count() }} {{ trans_choice('locations.rooms',$campus->rooms()->count()) }}</span>
                            </div>
                        </div>
                        <div class="col-3">
                            <h5>{{ __('locations.campus.information.levels') }}</h5>
                            <dl class="row">
                                @foreach($campus->levels as $level)
                                    <dt class="col-sm-6">{{ $level }}:</dt>
                                    <dd class="col-sm-6">{{ $campus->students()->where('level_id', $level->id)->count() }}</dd>
                                @endforeach
                            </dl>
                        </div>
                        <div class="col-1">
                            <div class="d-flex flex-column align-items-center">
                                <a
                                    class="btn btn-primary mb-2"
                                    role="button"
                                    href="{{ route('locations.campuses.show', ['campus' => $campus->id]) }}"
                                    title="{{ __('common.view') }}"
                                ><i class="fa fa-eye"></i></a>
                                @can('has-permission', 'locations.buildings')
                                <a
                                    class="btn btn-info mb-2"
                                    role="button"
                                    @if($campus->buildings()->count() == 1)
                                        href="{{ route('locations.buildings.show', ['building' => $campus->buildings()->first()->id]) }}"
                                    @else
                                        href="{{ route('locations.buildings.index') }}"
                                    @endif
                                    title="{{ trans_choice('locations.buildings',$campus->buildings()->count()) }}"
                                ><i class="fa-solid fa-building"></i></a>
                                @endcan
                                @can('viewAny', \App\Models\SubjectMatter\Subject::class, $campus)
                                    <a
                                        class="btn btn-dark mb-2"
                                        role="button"
                                        href="{{ route('subjects.subjects.index', ['campus' => $campus->id]) }}"
                                        title="{{ trans_choice('subjects.subject', $campus->subjects()->count()) }}"
                                    ><i class="fa-solid fa-book"></i></a>
                                @endcan
                                @if($campus->canDelete())
                                    <button
                                        class="btn btn-danger mb-2"
                                        role="button"
                                        onclick="confirmDelete('{{ __('locations.campus.delete.confirm') }}', '{{ route('locations.campuses.destroy', ['campus' => $campus->id]) }}')"
                                        title="{{ __('common.delete') }}"
                                    ><i class="fa-solid fa-times"></i></button>
                                @endif
                                <div class="btn-group btn-group-sm mt-3" role="group">
                                    <a
                                        role="button"
                                        class="btn btn-primary"
                                        @if($loop->first)
                                            href="#"
                                            disabled
                                        @else
                                            href="{{ route('locations.campuses.update.order', ['campus' => $campus->id, 'order' => ($loop->iteration - 1)]) }}"
                                        @endif
                                        title="{{ __('common.move.up') }}"
                                    ><i class="fa-solid fa-up-long"></i></a>
                                    <a
                                        role="button"
                                        class="btn btn-primary"
                                        @if($loop->last)
                                            href="#"
                                            disabled
                                        @else
                                            href="{{ route('locations.campuses.update.order', ['campus' => $campus->id, 'order' => ($loop->iteration + 1)]) }}"
                                        @endif
                                        title="{{ __('common.move.down') }}"
                                    ><i class="fa-solid fa-down-long"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
