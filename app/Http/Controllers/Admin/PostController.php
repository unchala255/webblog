<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author', 'category'])->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:posts,slug',
            'content' => 'required',
            'excerpt' => 'nullable',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['author_id'] = auth()->id();

        Post::create($validated);

        return redirect()->route('admin.posts.index')->with('success', 'บทความถูกบันทึกเรียบร้อยแล้ว');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:posts,slug,' . $post->id,
            'content' => 'required',
            'excerpt' => 'nullable',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', 'บทความถูกอัปเดตเรียบร้อยแล้ว');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'ลบบทความเรียบร้อยแล้ว');
    }
}
