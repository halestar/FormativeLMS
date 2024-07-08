<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogPostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except('show');
    }
    public function index()
    {
        return view('cms.index');
    }
    public function show(BlogPost $post)
    {
        return view('cms.post', compact('post'));
    }

    public function create()
    {
        return view('cms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'posted_by' => 'required',
            'body' => 'required',
        ]);
        Log::debug($data['body']);
        $post = new BlogPost();
        $post->fill($data);
        $post->save();
        return redirect()->route('cms.posts.index');
    }

    public function edit(Request $request, BlogPost $post)
    {
        return view('cms.edit', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'posted_by' => 'required',
            'body' => 'required',
        ]);
        Log::debug($data['body']);
        $post->fill($data);
        $post->save();
        return redirect()->route('cms.posts.index');
    }
}
