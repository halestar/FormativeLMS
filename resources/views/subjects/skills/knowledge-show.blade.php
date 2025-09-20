@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
<div class="container">
    <div class="border-bottom mb-3 d-flex justify-content-between align-items-center">
        <h3>
            {{ $skill->designation }}
            <span
                class="ms-3 badge"
                style="background-color: {{ $skill->subject->color }}; text: {{ $skill->subject->getTextHex() }};"
            >{{ $skill->subject->name }}</span>
            @if($skill->active)
                <span class="ms-3 badge text-bg-success">{{ __('common.active') }}</span>
            @else
                <span class="ms-3 badge text-bg-danger">{{ __('common.inactive') }}</span>
            @endif
        </h3>
        <a href="{{ route('subjects.skills.edit.knowledge', $skill) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-edit"></i></a>
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
                        href="{{ route('subjects.skills.rubric.knowledge', ['skill' => $skill->id]) }}"
                        class="btn btn-primary btn-sm"
                        role="button"
                    ><i class="fa-solid fa-edit"></i></a>
                @else
                    <a
                        href="{{ route('subjects.skills.rubric.knowledge', ['skill' => $skill->id]) }}"
                        class="btn btn-primary btn-sm"
                        role="button"
                    ><i class="fa-solid fa-plus"></i></a>
                @endif
            </div>
            @if(!$skill->rubric)
                <div class="alert alert-warning">{{ __('subjects.skills.rubric.no') }}</div>
            @else
                <x-assessment.rubric-viewer :rubric="$skill->rubric" />
            @endif
        </div>
    </div>
    <div class="alert alert-info">
        <h5 class="alert-heading border-bottom mb-2">{{ __('subjects.skills.levels.applicable') }}</h5>
        <p>{{ $skill->levels->pluck('name')->join(', ') }}</p>
    </div>
    <div class="mb-3 border-bottom d-flex justify-content-between align-items-between">
        <h5>{{ trans_choice('subjects.skills.category', $skill->categories()->count()) }}</h5>
        <button type="button" class="btn btn-primary btn-sm" onclick="$('#link-container').removeClass('d-none')">{{ __('subjects.skills.category.link') }}</button>
    </div>
    <form action="{{ route('subjects.skills.link.knowledge', $skill) }}" method="POST">
        @csrf
        <div class="alert alert-secondary d-none" id="link-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group">
                    <span class="input-group-text">{{ __('subjects.skills.category.designation') }}</span>
                    <select class="form-select" name="cat_designation_id" id="cat_designation_id">
                        @foreach(\App\Models\CRUD\SkillCategoryDesignation::whereNotIn('id', $skill->categories->pluck('info.designation_id'))->get() as $catDesignation)
                            <option value="{{ $catDesignation->id }}">{{ $catDesignation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="fw-bolder fs-4 mx-2">:</span>
                <livewire:assessment.category-selector/>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary me-3">{{ __('subjects.skills.category.link') }}</button>
                <button type="button" class="btn btn-secondary" onclick="$('#link-container').addClass('d-none')">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </form>
    <ul class="list-group">
        @foreach($skill->categories as $category)
            <li class="list-group-item list-group-item-action fs-4 d-flex justify-content-between align-items-center">
                <span><strong>{{ $category->info->designation->name }}:</strong> {{ $category->name }}</span>
                <div>
                    <a
                        href="#"
                        class="btn btn-primary btn-sm me-2"
                        role="button"
                    ><i class="fa-solid fa-edit"></i></a>
                    @if($skill->categories->count() > 1)
                    <a
                        href="#"
                        class="btn btn-danger btn-sm"
                        role="button"
                        onclick="confirmDelete('{{ __('subjects.skills.category.unlink.confirm') }}', '{{ route('subjects.skills.unlink.knowledge', ['skill' => $skill->id, 'category' => $category->id]) }}')"
                    ><i class="fa-solid fa-times"></i></a>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
@endsection
