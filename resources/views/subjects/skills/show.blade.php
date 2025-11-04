@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container">
        <div class="border-bottom mb-3 d-flex justify-content-between align-items-center">
            <h3 class="d-flex justify-content-start align-items-center">
                {{ $skill->designation }}
                @if($skill->isGlobal())
                    <span
                            class="ms-3 badge text-bg-success"
                    >{{ trans_choice('subjects.skills.global',1) }}</span>
                @else
                    @foreach($skill->subjects as $subject)
                        <div class="ms-3 d-flex justify-content-center align-items-center">
                            <span
                                    class="m-0 badge"
                                    style="background-color: {{ $subject->color }}; text: {{ $subject->getTextHex() }};"
                            >{{ $subject->name }}</span>
                            <span class="badge m-0 p-0">{!! $subject->campus->iconHtml('normal') !!}</span>
                        </div>
                    @endforeach
                @endif
                @if($skill->active)
                    <span class="ms-3 badge text-bg-success">{{ __('common.active') }}</span>
                @else
                    <span class="ms-3 badge text-bg-danger">{{ __('common.inactive') }}</span>
                @endif
            </h3>
            <div>
                <a href="{{ route('subjects.skills.edit', $skill) }}" class="btn btn-primary btn-sm"><i
                            class="fa-solid fa-edit"></i></a>
                @if($skill->canDelete())
                    <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            onclick="confirmDelete('{{ __('subjects.skills.delete.confirm') }}', '{{ route('subjects.skills.delete', $skill) }}')"

                    ><i class="fa-solid fa-times"></i></button>
                @endif
            </div>
        </div>
        @if($skill->name)
            <h4 class="mb-3">{{ __('subjects.skills.name') }}: {{ $skill->name }}</h4>
        @endif
        <div class="row border-bottom mb-3">
            <div class="col-lg-4">
                <h5 class="mb-3 border-bottom">{{ __('subjects.skills.description') }}</h5>
                {!! $skill->description !!}
            </div>
            <div class="col-lg-8">
                <div class="mb-3 d-flex justify-content-between align-items-baseline border-bottom">
                    <h5>{{ __('subjects.skills.rubric') }}</h5>
                    @if($skill->rubric)
                        <a
                                href="{{ route('subjects.skills.rubric', ['skill' => $skill->id]) }}"
                                class="btn btn-primary btn-sm"
                                role="button"
                        ><i class="fa-solid fa-edit"></i></a>
                    @else
                        <a
                                href="{{ route('subjects.skills.rubric', ['skill' => $skill->id]) }}"
                                class="btn btn-primary btn-sm"
                                role="button"
                        ><i class="fa-solid fa-plus"></i></a>
                    @endif
                </div>
                @if(!$skill->rubric)
                    <div class="alert alert-warning">{{ __('subjects.skills.rubric.no') }}</div>
                @else
                    <x-assessment.rubric-viewer :rubric="$skill->rubric"/>
                @endif
            </div>
        </div>
        <div class="alert alert-info">
            <h5 class="alert-heading border-bottom mb-2">{{ trans_choice('subjects.skills.level', $skill->levels->count()) }}</h5>
            <p>{{ $skill->levels->pluck('name')->join(', ') }}</p>
        </div>

        @if(!$skill->isGlobal())
        <div class="mb-3 border-bottom d-flex justify-content-between align-items-between">
            <h5>{{ trans_choice('subjects.skills.category', $skill->categories()->count()) }}</h5>
            <button type="button" class="btn btn-primary btn-sm"
                    onclick="$('#link-container').removeClass('d-none')">{{ __('subjects.skills.category.link') }}</button>
        </div>
        <form action="{{ route('subjects.skills.link', $skill) }}" method="POST">
            @csrf
            <div class="alert alert-secondary d-none" id="link-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="input-group">
                        <span class="input-group-text">{{ __('subjects.skills.category.designation') }}</span>
                        <input type="text" class="form-control" name="cat_designation" id="cat_designation">
                    </div>
                    <span class="fw-bolder fs-4 mx-2">:</span>
                    <livewire:assessment.category-selector/>
                </div>
                <div class="d-flex justify-content-end align-items-center">
                    <button type="submit"
                            class="btn btn-primary me-3">{{ __('subjects.skills.category.link') }}</button>
                    <button type="button" class="btn btn-secondary"
                            onclick="$('#link-container').addClass('d-none')">{{ __('common.cancel') }}</button>
                </div>
            </div>
        </form>
        <ul class="list-group">
            @foreach($skill->categories as $category)
                <li class="list-group-item list-group-item-action fs-4 d-flex justify-content-between align-items-center">
                    <span><strong>{{ $category->designation->designation }}:</strong> {{ $category->name }}</span>
                    <div>
                        @if($skill->categories->count() > 1)
                            <a
                                    href="#"
                                    class="btn btn-danger btn-sm"
                                    role="button"
                                    onclick="confirmDelete('{{ __('subjects.skills.category.unlink.confirm') }}', '{{ route('subjects.skills.unlink', ['skill' => $skill->id, 'category' => $category->id]) }}')"
                            ><i class="fa-solid fa-times"></i></a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
        @endif
    </div>
@endsection
