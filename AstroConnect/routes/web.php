<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AstrologerController;
use App\Http\Controllers\AstrologerApplicationController;
use App\Http\Controllers\Admin\AdminAstrologerController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\EnsureUserIsAstrologer;
use App\Http\Middleware\IsUser;

Route::view('/', 'home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/services', 'pages.services')->name('services');
Route::view('/horoscope', 'pages.horoscope')->name('horoscope');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/contact', 'pages.contact')->name('contact');

Route::get('/astrologers', [AstrologerController::class, 'index'])->name('astrologers.index');
Route::get('/astrologers/{astrologer}', [AstrologerController::class, 'show'])->name('astrologers.show');

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
        return view('pages.admin.index');
    })->name('admin.dashboard');
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/astrologer/apply', [AstrologerApplicationController::class, 'create'])->name('astrologer.apply');
    Route::post('/astrologer/apply', [AstrologerApplicationController::class, 'store'])->name('astrologer.apply.store');
});

Route::prefix('astrologer')->middleware(['auth', EnsureUserIsAstrologer::class])->group(function () {
    Route::get('/dashboard', [AstrologerController::class, 'dashboard'])->name('astrologer.dashboard');
    Route::get('/profile', [AstrologerController::class, 'profile'])->name('astrologer.profile');
    Route::patch('/profile', [AstrologerController::class, 'update'])->name('astrologer.profile.update');
    Route::get('/appointments', [AstrologerController::class, 'appointments'])->name('astrologer.appointments');
});

Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/astrologers', [AdminAstrologerController::class, 'index'])->name('admin.astrologers.index');
    Route::patch('/astrologers/{astrologer}', [AdminAstrologerController::class, 'update'])->name('admin.astrologers.update');
    Route::patch('/astrologers/{astrologer}/approve', [AdminAstrologerController::class, 'approve'])->name('admin.astrologers.approve');
    Route::patch('/astrologers/{astrologer}/reject', [AdminAstrologerController::class, 'reject'])->name('admin.astrologers.reject');
});

require __DIR__.'/auth.php';
