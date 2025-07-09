@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <div class="row ps-4">
        <div class="col-md-1 align-self-end">{{ __('people.portrait') }}</div>
        <div class="col-md-4 align-self-end">{{ __('people.name') }}</div>
        <div class="col-md-2 align-self-end text-center">{{ __('people.primary_roles') }}</div>
        <div class="col-5 align-self-center text-end">
            @can('create', \App\Models\People\Person::class)
                <a
                    href="{{ route('people.create') }}"
                    role="button"
                    class="btn btn-success"
                >
                    {!! __('people.add_new_person') !!}
                </a>
            @endcan
        </div>
    </div>
    <ul class="list-group">
        @foreach($people as $person)
            <li class="list-group-item list-group-item-action">
                <div class="row">
                    <div class="col-md-1">
                        <img
                            class="img-fluid img-thumbnail"
                            src="{{ $person->thumbnail_url }}"
                            alt="{{ __('people.profile.image') }}"
                        />
                    </div>
                    <h3 class="col-md-4 align-self-center ">{{ $person->name }}</h3>
                    <div class="col-2 align-self-center text-center">
                        @if($person->isStudent())
                            <span class="badge text-bg-primary">{{ __('common.student') }}</span>
                            <span class="badge text-bg-info">{{ $person->student()->level->name }}</span>
                        @endif
                        @if($person->isParent())
                            <span class="badge text-bg-primary">{{ __('common.parent') }}</span>
                            @foreach($person->parentCampuses() as $campus)
                                <span class="badge text-bg-info">{{ $campus->abbr }}</span>
                            @endforeach
                        @endif
                        @if($person->isEmployee())
                            <span class="badge text-bg-primary">{{ trans_choice('people.employee', 1) }}</span>
                            @foreach($person->employeeCampuses as $campus)
                                <span class="badge text-bg-info">{{ $campus->abbr }}</span>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-5 align-self-center text-end">
                        <a
                            role="button"
                            class="btn btn-primary"
                            href="{{ route('people.show', ['person' => $person->school_id]) }}"
                        ><i class="fa fa-eye"></i></a>
                        @can('update', $person)
                            <a
                                role="button"
                                class="btn btn-danger"
                                href="{{ route('people.edit', ['person' => $person->school_id]) }}"
                            ><i class="fa fa-edit"></i></a>
                        @endcan
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="mt-1">
        {{ $people->links() }}
    </div>
</div>
@endsection
