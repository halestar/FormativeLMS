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
                            @if($self->canViewField($self->viewableField('portrait_url'),$person) && $person->thumbnail_url)
                                src="{{ $person->thumbnail_url }}"
                            @else
                                src='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>'
                            @endif
                            alt="{{ __('people.profile.image') }}"
                        />
                    </div>
                    <h3 class="col-md-4 align-self-center ">{{ $person->name }}</h3>
                    <div class="col-2 align-self-center text-center">
                        @if($person->isStudent())
                            <span class="badge text-bg-primary">{{ __('common.student') }}</span>
                        @endif
                        @if($person->isParent())
                            <span class="badge text-bg-primary">{{ __('common.parent') }}</span>
                        @endif
                        @if($person->isEmployee())
                            <span class="badge text-bg-primary">{{ __('people.employee') }}</span>
                        @endif
                    </div>
                    <div class="col-5 align-self-center text-end">
                        <a
                            role="button"
                            class="btn btn-primary"
                            href="{{ route('people.show', ['person' => $person->id]) }}"
                        ><i class="fa fa-eye"></i></a>
                        @can('update', $person)
                            <a
                                role="button"
                                class="btn btn-danger"
                                href="{{ route('people.edit', ['person' => $person->id]) }}"
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
