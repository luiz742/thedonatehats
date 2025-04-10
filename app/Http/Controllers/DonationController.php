<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $wallet = $user?->wallet;

        $deposits = [];

        if ($wallet) {
            // Chama o método do WalletController ou refatora o código de lá para cá
            $deposits = app(WalletController::class)->getIncomingDepositsFormatted($wallet->address);
        }

        return Inertia::render('Donation/Index', [
            'donations' => Donation::with('user')->latest()->get(),
            'wallet' => $wallet,
            'deposits' => $deposits,
        ]);
    }

    public function history()
    {
        $user = Auth::user();
        $wallet = $user?->wallet;
        $deposits = [];

        if ($wallet) {
            $depositResult = app(WalletController::class)->checkDeposits($wallet->address);
            if ($depositResult['success']) {
                $deposits = $depositResult['usdt_deposits'];
            }
        }

        // dd($deposits);

        return Inertia::render('Donation/History', [
            'donations' => Donation::with('user')->where('user_id', auth()->id())->latest()->get(),
            'wallet' => $wallet,
            'deposits' => $deposits,
        ]);
    }



    public function balance()
    {
        return Inertia::render('Donation/Balance', [
            'donations' => Donation::with('user')->where('user_id', auth()->id())->latest()->get()
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'amount' => 'required|in:100,500,1000,2000',
    //     ]);

    //     $donation = Donation::create([
    //         'user_id' => auth()->id(),
    //         'amount' => $request->amount,
    //         'status' => 'pending',
    //         'expires_at' => now()->addMinutes(15),

    //     ]);

    //     return redirect()->back();
    // }
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|in:100,500,1000,2000',
        ]);

        $donation = Donation::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
        ]);

        $wallet = auth()->user()->wallet;
        if ($wallet) {
            app(WalletController::class)->tryConfirmPendingDonation($wallet, $donation);
        }

        return response()->json(['donation' => $donation]);
    }

}
