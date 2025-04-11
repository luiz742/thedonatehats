<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index()
    {
        $kycs = Kyc::with('user')->latest()->get();
        return inertia('Admin/KYC/Index', ['kycs' => $kycs]); // ou view(), se usar blade
    }

    public function show($id)
    {
        $kyc = Kyc::with('user')->findOrFail($id);
        return inertia('Admin/KYC/Show', ['kyc' => $kyc]); // ou view()
    }

    public function approve($id)
    {
        $kyc = Kyc::findOrFail($id);
        $kyc->status = 'approved';
        $kyc->save();

        return back()->with('success', 'KYC aprovado com sucesso.');
    }

    public function reject($id)
    {
        $kyc = Kyc::findOrFail($id);
        $kyc->status = 'rejected';
        $kyc->save();

        return back()->with('error', 'KYC rejeitado.');
    }
}
