<?php

use App\Models\Colocation;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'check.banned'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth', 'check.banned'])
    ->name('profile');

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::view('colocations/create', 'colocations.create')->name('colocations.create');
    Volt::route('colocations/{colocation}', 'colocations.show')->name('colocations.show');
    Volt::route('invitations/{token}', 'invitations.show')->name('invitations.show');
});

require __DIR__.'/auth.php';
