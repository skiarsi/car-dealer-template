<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home')->name('home');
Route::livewire('/inventory', 'inventory')->name('inventory');
Route::livewire('/vehicles/{vehicle}', 'vehicle-show')->name('vehicles.show');
Route::livewire('/contact', 'contact')->name('contact');
Route::livewire('/privacy-policy', 'privacy-policy')->name('legal.privacy');
Route::livewire('/cookie-policy', 'cookie-policy')->name('legal.cookies');
Route::livewire('/terms-of-use', 'terms-of-use')->name('legal.terms');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(array_key_exists($locale, config('localization.locales')), 404);

    return back()->withCookie(
        cookie()->forever(config('localization.cookie'), $locale)
    );
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'auth.login')->name('login');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::livewire('/dashboard/vehicles', 'admin.vehicles')->name('admin.vehicles');
    Route::livewire('/dashboard/vehicles/create', 'admin.vehicle-form')->name('admin.vehicles.create');
    Route::post('/dashboard/photos', \App\Http\Controllers\Admin\VehiclePhotoUploadController::class)->name('admin.photos.store');
    Route::post('/dashboard/logo', \App\Http\Controllers\Admin\DealershipLogoController::class)->name('admin.logo.store');
    Route::livewire('/dashboard/vehicles/{vehicle}/edit', 'admin.vehicle-form')->name('admin.vehicles.edit');
    Route::livewire('/dashboard/hours', 'admin.hours')->name('admin.hours');
    Route::livewire('/dashboard/links', 'admin.external-links')->name('admin.links');
    Route::livewire('/dashboard/inquiries', 'admin.inquiries')->name('admin.inquiries');
});
