<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\BlogPost;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'published' => 'nullable|boolean',
        ]);
        $post->fill($data);
        $post->save();
        return redirect()->route('cms.posts.index');
    }

    public function listImgs()
    {
        $imgs = [];
        foreach(BlogPost::files() as $path)
            $imgs[] =
                [
                    'title' => basename($path),
                    'value' => Storage::disk('cms')->url($path),
                ];
        return response()->json($imgs, 200);
    }
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|image']);
        $img = $request->file('file');
        $path = BlogPost::uploadFile($img);
        return response()->json(['location' => $path], 200);
    }
}
