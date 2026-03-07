<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Middleware\IsAdmin;
// use App\Http\Middleware\IsAstrologer;
use App\Http\Middleware\IsUser;

Route::get('/', function () {
    return view('welcome');
});

/// only for user Route
Route::middleware(['auth', IsUser::class])->group(function () {
    // User-specific routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/// only for admin Route
Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('dashboard', function () {
        return view('admin.index');
    })->name('admin.dashboard');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
