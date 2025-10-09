<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Websites;
use App\Http\Controllers\Sources;
use App\Http\Controllers\Categories;

use App\Http\Controllers\Clusters;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Reports;
use App\Http\Controllers\Users;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

##### Web Routes ######

Route::get('/', function () {
    return redirect('/login');
});

# Dashboard
Route::prefix('dashboard')->middleware(['auth', 'verified'])->group( function()
{
    Route::get('list',[Websites::class, 'index'])->name('dashboard');
    // Route::get('/',[Dashboard::class, 'index'])->name('dashboard');
});

# Website
Route::prefix('website')->middleware('auth')->group( function()
{
    Route::get('list',[Websites::class, 'index'])->name('website');
    Route::get('edit/{id?}',[Websites::class, 'edit'])->name('website-edit');
    Route::post('store',[Websites::class,'store'])->name('website-store');

    Route::get('web-posts/{websiteId}',[Websites::class, 'postsList'])->name('website-posts');

    Route::prefix('web-source')->middleware('auth')->group( function()
    {
        Route::get('list/{websiteId}',[Websites::class, 'wSourceIndex'])->name('website-source');
        Route::post('store',[Websites::class,'wSourceStore'])->name('website-source-store');
    });
});
Route::prefix('website-queue')->middleware('auth')->group( function()
{
    Route::get('list',[Websites::class, 'postsQueueList'])->name('queue');
    Route::post('store',[Websites::class,'postsQueueStore'])->name('queue-store');
});
Route::prefix('website-posts')->middleware('auth')->group( function()
{
    Route::get('list',[Websites::class, 'postsList'])->name('posts');
});

# Admin
Route::prefix('source')->middleware('auth')->group( function()
{
    Route::get('list',[Sources::class, 'index'])->name('source');
    Route::get('edit/{id?}',[Sources::class, 'edit'])->name('source-edit');
    Route::post('store',[Sources::class,'store'])->name('source-store');
});

Route::prefix('source-posts')->middleware('auth')->group( function()
{
    Route::get('list',[Sources::class, 'sourcePostList'])->name('source-post');
    Route::post('store',[Sources::class,'sourcePostStore'])->name('source-post-store');
});

Route::prefix('category')->middleware('auth')->group( function()
{
    Route::get('list',[Categories::class, 'index'])->name('category');
    Route::get('edit/{id?}',[Categories::class, 'edit'])->name('category-edit');
    Route::post('store',[Categories::class,'store'])->name('category-store');
});

# Report
Route::prefix('report')->middleware('auth')->group( function()
{
    Route::get('posts',[Reports::class, 'posts'])->name('report_post');
    Route::get('ads',[Reports::class, 'ads'])->name('report_ads');
});

# User
Route::prefix('user')->middleware('auth')->group( function()
{
    Route::get('',[Users::class, 'index'])->name('users');
    Route::post('',[Users::class, 'index'])->name('users-filter');
    Route::get('/edit/{id?}', [Users::class, 'edit'])->name('user-edit');
    Route::post('store',[Users::class,'store'])->name('user-store');
});


#####
# API
Route::prefix('api')->withoutMiddleware([VerifyCsrfToken::class])->group( function()
{
    Route::post('keyword',[Websites::class,'keyword'])->name('website-keyword');
});



// Route::get('/',[Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

# User Profile
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
