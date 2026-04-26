<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $category_id = $request->query('category');
        $search = $request->query('search');

        $query = Post::with(['author', 'category'])->where('status', 'published');

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(9);
        $categories = Category::orderBy('name')->get();

        return view('index', compact('posts', 'categories', 'category_id', 'search'));
    }
}
