@extends('layouts.front')

@section('header')
<header class="masthead" style="background-image: url('/images/cms/web_back.webp')">
    <div class="container position-relative px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-7">
                <div class="site-heading">
                    <h1>Formative Assessment - Based Learning Management System</h1>
                    <span class="subheading">A development blog for an LMS</span>
                </div>
            </div>
        </div>
    </div>
</header>
@endsection
@section('content')
<div class="container px-4 px-lg-5">
    <div class="row gx-4 gx-lg-5 justify-content-center">
        <div class="col-md-10 col-lg-8 col-xl-7">
            @foreach(\App\Models\CMS\BlogPost::published()->orderBy('created_at', 'desc')->get() as $post)
            <!-- Post preview-->
            <div class="post-preview">
                <a href="{{ route('blog.show', ['post' => $post->id]) }}">
                    <h2 class="post-title">{{ $post->title }}</h2>
                    <h3 class="post-subtitle">{{ $post->slug }}</h3>
                </a>
                <p class="post-meta">
                    Posted by
                    <a href="#!">{{ $post->posted_by }}</a>
                    on {{ $post->created_at->format('m/d/Y') }}
                </p>
            </div>
            <!-- Divider-->
            <hr class="my-4" />
            @endforeach
        </div>
    </div>
</div>
@endsection
