<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Colocation\BalanceController;
use App\Http\Controllers\Colocation\CategoryController;
use App\Http\Controllers\Colocation\ColocationController;
use App\Http\Controllers\Colocation\ExpenseController;
use App\Http\Controllers\Colocation\InvitationController;
use App\Http\Controllers\Colocation\LeaveController;
use App\Http\Controllers\Colocation\MemberRoleController;
use App\Http\Controllers\Colocation\PaymentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'check.banned'])
    ->name('dashboard');

// Admin routes
Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('admin/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('admin/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::post('admin/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('admin.users.unban');
});

// Profile routes
Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Colocation routes
Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::delete('colocations/{colocation}', [ColocationController::class, 'destroy'])->name('colocations.destroy');
    
    // Leave colocation
    Route::delete('colocations/{colocation}/leave', [LeaveController::class, 'destroy'])->name('colocations.leave');
    
    // Balances
    Route::get('colocations/{colocation}/balances', [BalanceController::class, 'show'])->name('colocations.balances');
    
    // Payments
    Route::post('colocations/{colocation}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('colocations/{colocation}/payments', [PaymentController::class, 'index'])->name('payments.index');
    
    // Expenses
    Route::post('colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    
    // Categories
    Route::get('colocations/{colocation}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Invitations
    Route::get('colocations/{colocation}/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('colocations/{colocation}/invitations', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('invitations/{token}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');
    
    // Member management
    Route::post('colocations/{colocation}/members/{user}/promote', [MemberRoleController::class, 'promoteToOwner'])->name('members.promote');
    Route::delete('colocations/{colocation}/members/{user}', [MemberRoleController::class, 'removeMember'])->name('members.remove');
});

require __DIR__.'/auth.php';
