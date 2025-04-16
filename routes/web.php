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

Route::get('/test-check/{address}', [WalletController::class, 'checkDeposits']);

// Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {

// });


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/generate', [WalletController::class, 'generate'])->name('wallet.generate');
});

Route::post('/wallets/{wallet}/withdraw', [WalletController::class, 'withdraw'])->name('wallets.withdraw');


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/kyc', [UserController::class, 'updateKycStatus'])->name('users.kyc.update');

    Route::get('/kyc', [\App\Http\Controllers\Admin\KycController::class, 'index'])->name('admin.kycs.index');
    Route::get('/kyc/{id}', [\App\Http\Controllers\Admin\KycController::class, 'show'])->name('admin.kycs.show');
    Route::post('/kyc/{id}/approve', [\App\Http\Controllers\Admin\KycController::class, 'approve'])->name('admin.kycs.approve');
    Route::post('/kyc/{id}/reject', [\App\Http\Controllers\Admin\KycController::class, 'reject'])->name('admin.kycs.reject');

    Route::resource('donations', AdminDonationController::class)->only(['index', 'show', 'destroy']);
});


Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/cashback/create/{donationId}', [CashbackController::class, 'create'])->name('cashback.create');
    Route::post('/cashback/store/{donationId}', [CashbackController::class, 'store'])->name('cashback.store');
    Route::get('/cashback/{id}', [CashbackController::class, 'show'])->name('cashback.show');
    Route::post('/cashback/complete/{id}', [CashbackController::class, 'complete'])->name('cashback.complete');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/donations/history', [DonationController::class, 'history'])->name('donations.history');
    Route::get('/donations/balance', [DonationController::class, 'balance'])->name('donations.balance');
    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/donations', [DonationController::class, 'store'])->name('donations.store');
});


// Route::get('/', [DonationController::class, 'index'])->name('homepage');
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
