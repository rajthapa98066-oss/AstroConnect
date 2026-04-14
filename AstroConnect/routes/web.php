<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AstrologerController;
use App\Http\Controllers\AstrologerApplicationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AstrologerAppointmentController;
use App\Http\Controllers\AstrologerBlogController;
use App\Http\Controllers\AstrologerReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminAstrologerController;
use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\EnsureUserIsAstrologer;
use App\Http\Middleware\IsUser;
use App\Http\Middleware\RedirectApprovedAstrologerFromUserSide;
use App\Http\Middleware\RedirectAdminFromUserSide;
use Illuminate\Http\Request;

Route::middleware([RedirectApprovedAstrologerFromUserSide::class, RedirectAdminFromUserSide::class])->group(function () {
    Route::view('/', 'home')->name('home');
    Route::view('/about', 'pages.user.about')->name('about');
    Route::view('/services', 'pages.user.services')->name('services');
    Route::get('/horoscope', [App\Http\Controllers\HoroscopeController::class, 'index'])->name('horoscope');
Route::get('/horoscope/{sign}', [App\Http\Controllers\HoroscopeController::class, 'show'])->name('horoscope.show');
    Route::get('/blog', [BlogController::class, 'index'])->name('blog');
    Route::get('/blog/{blog:slug}', [BlogController::class, 'show'])->name('blog.show');
    Route::view('/contact', 'pages.user.contact')->name('contact');

    Route::get('/astrologers', [AstrologerController::class, 'index'])->name('astrologers.index');
    Route::get('/astrologers/{astrologer}', [AstrologerController::class, 'show'])->name('astrologers.show');
});

Route::middleware(['auth', RedirectApprovedAstrologerFromUserSide::class, RedirectAdminFromUserSide::class, IsUser::class])->group(function () {
    // User-specific routes
    Route::get('/home', function (Request $request) {
        abort_unless($request->user()?->canAccessUserPanel(), 403);

        return view('home');
    })->name('user.home');

    Route::post('/astrologers/{astrologer}/book', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/rating', [AppointmentController::class, 'rate'])->name('appointments.rate');
    Route::post('/astrologers/{astrologer}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/astrologers/{astrologer}/reports', [AstrologerReportController::class, 'store'])
        ->middleware('verified')
        ->name('astrologer.reports.store');
    Route::get('/my-appointments', [AppointmentController::class, 'userIndex'])->name('appointments.user.index');

    // Khalti Payment Routes
    Route::post('/appointments/{appointment}/pay', [App\Http\Controllers\KhaltiController::class, 'initiate'])->name('khalti.initiate');
    Route::get('/khalti/callback', [App\Http\Controllers\KhaltiController::class, 'callback'])->name('khalti.callback');
});

Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('dashboard', [AdminController::class, 'AdminDashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');

    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('admin.blogs.index');
    Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('admin.blogs.create');
    Route::post('/blogs', [AdminBlogController::class, 'store'])->name('admin.blogs.store');
    Route::get('/blogs/{blog}/edit', [AdminBlogController::class, 'edit'])->name('admin.blogs.edit');
    Route::patch('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('admin.blogs.update');
    Route::patch('/blogs/{blog}/visibility', [AdminBlogController::class, 'toggleVisibility'])->name('admin.blogs.visibility');
    Route::patch('/blogs/{blog}/approve', [AdminBlogController::class, 'approve'])->name('admin.blogs.approve');
    Route::patch('/blogs/{blog}/reject', [AdminBlogController::class, 'reject'])->name('admin.blogs.reject');
    Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('admin.blogs.destroy');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::patch('/reports/{report}/flag', [AdminReportController::class, 'flag'])->name('admin.reports.flag');
    Route::patch('/reports/{report}/disable', [AdminReportController::class, 'disable'])->name('admin.reports.disable');
    Route::delete('/reports/{report}/account', [AdminReportController::class, 'deleteAstrologer'])->name('admin.reports.delete-account');
});

Route::middleware(['auth', RedirectApprovedAstrologerFromUserSide::class, RedirectAdminFromUserSide::class])->group(function () {
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
    Route::get('/appointments', [AstrologerAppointmentController::class, 'index'])->name('astrologer.appointments');
    Route::patch('/appointments/{appointment}/status', [AstrologerAppointmentController::class, 'updateStatus'])->name('astrologer.appointments.status');

    Route::get('/blogs', [AstrologerBlogController::class, 'index'])->name('astrologer.blogs.index');
    Route::get('/blogs/create', [AstrologerBlogController::class, 'create'])->name('astrologer.blogs.create');
    Route::post('/blogs', [AstrologerBlogController::class, 'store'])->name('astrologer.blogs.store');
    Route::get('/blogs/{blog}/edit', [AstrologerBlogController::class, 'edit'])->name('astrologer.blogs.edit');
    Route::patch('/blogs/{blog}', [AstrologerBlogController::class, 'update'])->name('astrologer.blogs.update');
});

Route::prefix('admin')->middleware(['auth', IsAdmin::class])->group(function () {
    Route::get('/astrologers', [AdminAstrologerController::class, 'index'])->name('admin.astrologers.index');
    Route::patch('/astrologers/{astrologer}', [AdminAstrologerController::class, 'update'])->name('admin.astrologers.update');
    Route::patch('/astrologers/{astrologer}/approve', [AdminAstrologerController::class, 'approve'])->name('admin.astrologers.approve');
    Route::patch('/astrologers/{astrologer}/reject', [AdminAstrologerController::class, 'reject'])->name('admin.astrologers.reject');
});

require __DIR__ . '/auth.php';
