<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Auth\AuthorizationController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => false //Features::enabled(Features::registration()),
    ]);
})->name('home');

// Auth Flow
Route::get('authorize', [AuthorizationController::class, 'authorize'])->name('authorize');
Route::get('auth', [SocialLoginController::class, 'selectProvider'])->name('auth');
Route::get('login', [SocialLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [SocialLoginController::class, 'loginWithPassword'])->name('login.store');

Route::middleware(['throttle:6,1'])->group(function () {
    Route::get('login/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('login.redirect');
    Route::get('login/{provider}/callback', [
        SocialLoginController::class,
        'handleProviderCallback'
    ])->name('login.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('clients', ClientController::class);
        Route::resource('providers', ProviderController::class);
        Route::resource('users', UserController::class);
        Route::get('audits', [AuditController::class, 'index'])->name('audits.index');
    });
});

require __DIR__ . '/settings.php';