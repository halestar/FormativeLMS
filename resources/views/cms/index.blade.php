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
                    @foreach(\App\Models\CMS\BlogPost::orderBy('created_at', 'desc')->get() as $post)
                        <a
                            href="{{ route('cms.posts.edit', ['post' => $post->id]) }}"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        >
                            <div class="post-name">{{ $post->title }}</div>
                            <div>
                                created: {{ $post->created_at->format('m/d/Y h:i A') }}
                                @if($post->published)
                                    <span class="badge bg-primary ms-3">published</span>
                                @else
                                    <span class="badge bg-danger ms-3">unpublished</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
