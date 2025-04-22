<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user', 'donation')->latest()->get()->values();

        return inertia('Admin/Withdrawals/Index', [
            'withdrawals' => $withdrawals,
        ]);
    }

    public function edit($id)
    {
        $withdrawal = Withdrawal::with('user', 'donation')->findOrFail($id);

        return inertia('Admin/Withdrawals/Edit', [
            'withdrawal' => $withdrawal,
        ]);
    }


    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'approved';
        $withdrawal->approved_at = now();
        $withdrawal->save();

        return back()->with('success', 'Withdrawal approved successfully.');
    }

    public function reject($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);
        $withdrawal->status = 'rejected';
        $withdrawal->rejected_at = now();
        $withdrawal->save();

        return back()->with('success', 'Withdrawal rejected.');
    }
}
