<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Websites;

use App\Http\Controllers\Clusters;
use App\Http\Controllers\Categories;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\Reports;
use App\Http\Controllers\Users;

##### Web Routes ######

Route::get('/', function () {
    return redirect('/login');
});

# Dashboard
Route::prefix('dashboard')->middleware(['auth', 'verified'])->group( function()
{
    Route::get('/',[Dashboard::class, 'index'])->name('dashboard');
});

# Website
Route::prefix('website')->middleware('auth')->group( function()
{
    Route::get('list',[Websites::class, 'index'])->name('website');
    Route::get('edit/{id?}',[Websites::class, 'edit'])->name('website-edit');
    Route::post('store',[Websites::class,'store'])->name('website-store');
});

# Admin
Route::prefix('source')->middleware('auth')->group( function()
{
    Route::get('list',[Clusters::class, 'index'])->name('source');
});

#Category
Route::prefix('category')->middleware('auth')->group( function()
{
    Route::get('list',[Categories::class, 'index'])->name('category');
});



# Report
Route::prefix('report')->middleware('auth')->group( function()
{
    Route::get('campaign',[Reports::class, 'campaign'])->name('report_campaign');
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
Route::prefix('api')->middleware('auth')->group( function()
{
    Route::post('clusters-manage',[ApiController::class, 'clustersManage']);
});



// Route::get('/',[Dashboard::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

# User Profile
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
