<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show($id)
    {
        $post = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->findOrFail($id);

        // Increment view count
        $post->increment('view_count');

        return view('posts.show', compact('post'));
    }
}
