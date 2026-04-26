<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

// Auth routes from Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('posts', AdminPostController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('users', AdminUserController::class);
});
