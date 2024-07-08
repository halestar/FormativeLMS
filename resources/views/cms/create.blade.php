@extends('layouts.app')
@push('head_scripts')
    <x-tinymce-config />
@endpush

@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">Create Post</div>
        <form action="{{ route('cms.posts.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Post Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control"
                    value="{{ old('title') }}"
                />
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Post Slug</label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    class="form-control"
                    value="{{ old('slug') }}"
                />
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Posted By</label>
                <input
                    type="text"
                    name="posted_by"
                    id="posted_by"
                    class="form-control"
                    value="{{ old('posted_by') }}"
                />
            </div>
            <div class="mb-3">
                <label for="body" class="form-label">Post Body</label>
                <x-tinymce-editor name="body">{{ old('body') }}</x-tinymce-editor>
            </div>

            <div class="row">
                <button class="btn btn-primary col-md m-1" type="submit">Create Post</button>
                <a class="btn btn-secondary col-md m-1" role="button" href="{{ route('cms.posts.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

