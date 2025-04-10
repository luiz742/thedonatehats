<?php

// app/Http/Controllers/CashbackController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashback;
use App\Models\Donation;
use Inertia\Inertia;

class CashbackController extends Controller
{
    // Exibe o formulário para o usuário solicitar cashback
    public function create($donationId)
    {
        $donation = Donation::findOrFail($donationId);

        // Se a doação não foi completada, redireciona o usuário
        if ($donation->status !== 'completed') {
            return redirect()->route('donations.index')->with('error', 'Doação não completada!');
        }

        return Inertia::render('Cashback/Create', [
            'donation' => $donation
        ]);
    }

    // Processa a solicitação de cashback
    public function store(Request $request, $donationId)
    {
        $request->validate([
            'solana_wallet_address' => 'required|string',
        ]);

        $donation = Donation::findOrFail($donationId);

        // Verifica se a doação foi completada
        if ($donation->status !== 'completed') {
            return redirect()->route('donations.index')->with('error', 'Doação não completada!');
        }

        // Cria o cashback
        $cashback = Cashback::create([
            'donation_id' => $donation->id,
            'user_id' => auth()->id(),
            'solana_wallet_address' => $request->solana_wallet_address,
            'amount' => $donation->amount,  // O cashback é 100% do valor da doação
            'status' => 'pending'
        ]);

        return redirect()->route('cashback.show', $cashback->id)->with('success', 'Solicitação de cashback realizada!');
    }

    // Exibe a solicitação de cashback
    public function show($id)
    {
        $cashback = Cashback::findOrFail($id);

        return Inertia::render('Cashback/Show', [
            'cashback' => $cashback
        ]);
    }

    // Função para atualizar o status do cashback para "completado"
    public function complete($id)
    {
        $cashback = Cashback::findOrFail($id);
        $cashback->status = 'completed';
        $cashback->save();

        // Aqui você poderia adicionar a lógica para realizar a transferência de Shisha Coin para o endereço Solana

        return redirect()->route('cashback.show', $cashback->id)->with('success', 'Cashback enviado!');
    }
}
