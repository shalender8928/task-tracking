<?php

use App\Livewire\LeaveForm;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\TimeTracker;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Route::get('/time-tracker', TimeTracker::class)->name('timetrackers.index');
    Route::get('/leave', LeaveForm::class)->name('leave.form');
});

require __DIR__.'/auth.php';
