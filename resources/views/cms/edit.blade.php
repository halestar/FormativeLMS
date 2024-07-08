@extends('layouts.app')
@push('head_scripts')
    <x-tinymce-config />
@endpush

@section('content')
    <div class="container">
        <div class="border-bottom display-4 text-primary mb-5">Update Post</div>
        <form action="{{ route('cms.posts.update', ['post' => $post->id]) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Post Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ $post->title }}"
                />
                <x-error-display key="title">{{ $errors->first('title') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Post Slug</label>
                <input
                    type="text"
                    name="slug"
                    id="slug"
                    class="form-control @error('slug') is-invalid @enderror"
                    value="{{ $post->slug }}"
                />
                <x-error-display key="slug">{{ $errors->first('slug') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Posted By</label>
                <input
                    type="text"
                    name="posted_by"
                    id="posted_by"
                    class="form-control @error('posted_by') is-invalid @enderror"
                    value="{{ $post->posted_by }}"
                />
                <x-error-display key="slug">{{ $errors->first('posted_by') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="body" class="form-label">Post Body</label>
                <x-tinymce-editor name="body">{{ $post->body }}</x-tinymce-editor>
            </div>

            <div class="row">
                <button class="btn btn-primary col-md m-1" type="submit">Update Post</button>
                <button
                    class="btn btn-danger col-md m-1"
                    type="button"
                    onclick="confirmDelete('u sure?', '{{ route('cms.posts.destroy', ['post' => $post->id]) }}')"
                >Delete Post</button>
                <a class="btn btn-secondary col-md m-1" role="button" href="{{ route('cms.posts.index') }}">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection

