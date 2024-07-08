@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <h4 class="card-header bg-primary-subtle d-flex justify-content-between align-items-center">
                <span class="card-title">Blog Posts</span>
                <div class="settings-controls">
                    <a class="btn btn-primary" href="{{ route('cms.posts.create') }}">New Post</a>
                </div>
            </h4>
            <div class="card=body">
                <div class="list-group">
                    @foreach(\App\Models\CMS\BlogPost::orderBy('created_at')->get() as $post)
                        <a
                            href="{{ route('cms.posts.edit', ['post' => $post->id]) }}"
                            class="list-group-item list-group-item-action"
                        >
                            <div class="post-name">{{ $post->title }}</div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
