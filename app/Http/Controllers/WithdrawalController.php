<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function request(Request $request)
    {
        // Validação do endereço da carteira Solana
        $request->validate([
            'solana_wallet' => 'required|string|min:5', // Verifique o tamanho correto do endereço
            'donation_id' => 'required|exists:donations,id' // Verifica se a doação existe
        ]);

        $user = Auth::user();
        $donation = Donation::findOrFail($request->donation_id);

        // Verificar se a doação pertence ao usuário e está completa
        if ($donation->user_id !== $user->id || $donation->status !== 'completed' || $donation->withdrawal) {
            return response()->json(['error' => 'Invalid donation for withdrawal.'], 422);
        }

        // Criando o pedido de saque
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'donation_id' => $request->donation_id,
            'amount' => $donation->amount / $donation->shisha_price, // Quantidade em SHISHA
            'solana_wallet' => $request->solana_wallet,
            'withdrawn_at' => now(),
        ]);

        // Marcando a doação como retirada
        $donation->withdrawal = true;
        $donation->save();

        return response()->json(['message' => 'Withdrawal request created successfully.']);
    }
}
