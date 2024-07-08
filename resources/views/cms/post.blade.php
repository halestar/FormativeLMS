@extends('layouts.front')
@section('header')
    <header class="masthead" style="background-image: url('/images/cms/web_back.webp')">
        <div class="container position-relative px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-7">
                    <div class="post-heading">
                        <h1>{{ $post->title }}</h1>
                        <h2 class="subheading">{{ $post->slug }}</h2>
                        <span class="meta">
                                Posted by
                                <a href="#!">{{ $post->posted_by }}</a>
                                on {{ $post->created_at->format('m/d/Y') }}
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection
@section('content')
    <article class="mb-4">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 justify-content-center">
                <div class="col-md-10 col-lg-8 col-xl-7">
                    {!! $post->body !!}
                </div>
            </div>
        </div>
    </article>
@endsection
