<?php

namespace App\Http\Controllers;

use App\Models\Kyc;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KycController extends Controller
{
    public function index()
    {
        $kyc = Kyc::where('user_id', auth()->id())->first();
        return Inertia::render('KYC/Form', ['kyc' => $kyc]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'document_type' => 'required|string',
            'document_number' => 'required|string|unique:kyc,document_number,' . auth()->id() . ',user_id',
            'document_image' => 'nullable|file|mimes:jpg,png,jpeg,pdf|max:4096',
            'selfie_image' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        $kyc = Kyc::updateOrCreateKyc(auth()->id(), $request->only([
            'full_name', 'date_of_birth', 'document_type', 'document_number'
        ]));

        if (!$kyc) {
            return redirect()->route('kyc.index')->with('error', 'Seu KYC já foi aprovado e não pode ser editado.');
        }

        if ($request->hasFile('document_image')) {
            $kyc->updateDocumentImage($request->file('document_image'));
        }

        if ($request->hasFile('selfie_image')) {
            $kyc->updateSelfieImage($request->file('selfie_image'));
        }

        return redirect()->route('kyc.index')->with('success', 'KYC enviado para análise.');
    }
}
