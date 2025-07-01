<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Blog::latest('published_at')->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|max:255',
            'image'        => 'nullable|image|max:2048',
            'excerpt'      => 'nullable|max:500',
            'body'         => 'required',
            'published_at' => 'nullable|date',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('blogs', 'public');
        }

        Blog::create($data);

        return redirect()->route('blog.index')
            ->with('status', 'Blog post created.');
    }

    public function show(Blog $blog)
    {
        return view('admin.blog.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'        => 'required|max:255',
            'image'        => 'nullable|image|max:2048',
            'excerpt'      => 'nullable|max:500',
            'body'         => 'required',
            'published_at' => 'nullable|date',
        ]);

        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('image')) {
            // delete old
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')
                ->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('blog.index')
            ->with('status', 'Blog post updated.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }
        $blog->delete();

        return redirect()->route('blog.index')
            ->with('status', 'Blog post deleted.');
    }
}
