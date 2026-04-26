<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'posts' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'categories' => Category::count(),
            'users' => User::count(),
            'views' => Post::sum('view_count'),
        ];

        $recent_posts = Post::with('author')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.index', compact('stats', 'recent_posts'));
    }
}
