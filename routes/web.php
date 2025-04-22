<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\CashbackController;
use App\Http\Controllers\KycController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\DonationController as AdminDonationController;
use App\Http\Controllers\WithdrawalController;

Route::post('/withdrawals', [WithdrawalController::class, 'store'])->middleware(['auth', 'verified']);

Route::get('/test-check/{address}', [WalletController::class, 'checkDeposits']);

// Protect routes that require authentication
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/generate', [WalletController::class, 'generate'])->name('wallet.generate');

    // Donation routes
    Route::get('/donations/history', [DonationController::class, 'history'])->name('donations.history');
    Route::get('/donations/balance', [DonationController::class, 'balance'])->name('donations.balance');
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');

    // Cashback routes
    Route::get('/cashback/create/{donationId}', [CashbackController::class, 'create'])->name('cashback.create');
    Route::post('/cashback/store/{donationId}', [CashbackController::class, 'store'])->name('cashback.store');
    Route::get('/cashback/{id}', [CashbackController::class, 'show'])->name('cashback.show');
    Route::post('/cashback/complete/{id}', [CashbackController::class, 'complete'])->name('cashback.complete');

    // KYC routes
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
});

// Admin Routes (only accessible by admins)
Route::prefix('admin')->name('admin.')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/kyc', [UserController::class, 'updateKycStatus'])->name('users.kyc.update');

    Route::get('/kyc', [\App\Http\Controllers\Admin\KycController::class, 'index'])->name('admin.kycs.index');
    Route::get('/kyc/{id}', [\App\Http\Controllers\Admin\KycController::class, 'show'])->name('admin.kycs.show');
    Route::post('/kyc/{id}/approve', [\App\Http\Controllers\Admin\KycController::class, 'approve'])->name('admin.kycs.approve');
    Route::post('/kyc/{id}/reject', [\App\Http\Controllers\Admin\KycController::class, 'reject'])->name('admin.kycs.reject');

    Route::resource('donations', AdminDonationController::class)->only(['index', 'show', 'destroy']);

    Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals/{id}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('withdrawals.reject');
    Route::get('/withdrawals/{id}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'edit'])->name('withdrawals.edit');
});

// Route for the homepage
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route for authenticated users (Dashboard)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
