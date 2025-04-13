<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Donation;

class DonationController extends Controller
{
    public function index()
    {
        $donations = Donation::with('user')->get();
        // dd($donations);
        return inertia('Admin/Donations/Index', [
            'donations' => $donations
        ]);
    }

    public function show($id)
    {
        $donation = Donation::with('user')->findOrFail($id);
        return inertia('Admin/Donations/Show', [
            'donation' => $donation
        ]);
    }

    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        $donation->delete();
        return redirect()->route('admin.donations.index')->with('success', 'Donation deleted successfully.');
    }
}
