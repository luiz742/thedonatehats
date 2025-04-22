<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'solana_wallet' => 'required|string|min:5',
            'donation_id' => 'required|exists:donations,id',
        ]);

        $user = Auth::user();
        $donation = Donation::findOrFail($request->donation_id);

        if ($donation->user_id !== $user->id || $donation->status !== 'completed' || $donation->withdrawal) {
            return redirect()->back()->dangerBanner('Invalid donation for withdrawal.');
        }

        Withdrawal::create([
            'user_id' => $user->id,
            'donation_id' => $donation->id,
            'amount' => $donation->amount / $donation->shisha_price,
            'solana_wallet' => $request->solana_wallet,
            'withdrawn_at' => now(),
        ]);

        $donation->withdrawal = true;
        $donation->save();

        return redirect()->back()->banner('Withdrawal request created successfully.');
    }
}
