<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Wallet;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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

    // Controller ajustado
    public function history()
    {
        $user = Auth::user();

        $donations = Donation::with('user')->where('user_id', $user->id)->latest()->get();

        return Inertia::render('Donation/History', [
            'donations' => $donations,
        ]);
    }

    public function fetchDeposits()
    {
        $user = Auth::user();
        $donations = Donation::where('user_id', $user->id)->get();
        $deposits = [];

        foreach ($donations as $donation) {
            if ($donation->wallet_address) {
                $result = app(WalletController::class)->checkDeposits($donation->wallet_address);
                if ($result['success'] && !empty($result['usdt_deposits'])) {
                    foreach ($result['usdt_deposits'] as $deposit) {
                        $deposits[] = $deposit;
                    }
                }
            }
        }

        return response()->json([
            'deposits' => $deposits
        ]);
    }

    public function balance()
    {
        return Inertia::render('Donation/Balance', [
            'donations' => Donation::with('user')->where('user_id', auth()->id())->latest()->get()
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|in:100,500,1000,2000',
        ]);

        // Gera uma nova carteira
        $pathToScript = base_path('tronweb/generate_wallet.js');
        $command = "node " . escapeshellarg($pathToScript);
        $output = shell_exec($command);

        if (!$output) {
            return response()->json(['error' => 'Erro ao gerar a carteira.'], 500);
        }

        $walletData = json_decode($output);

        if (!$walletData || !isset($walletData->address)) {
            return response()->json(['error' => 'Carteira inválida retornada.'], 500);
        }

        // Cria a carteira no banco
        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'address' => $walletData->address,
            'private_key' => Crypt::encryptString($walletData->privateKey),
            'public_key' => $walletData->publicKey,
        ]);

        // Cria a doação vinculada à carteira recém-criada
        $donation = Donation::create([
            'user_id' => auth()->id(),
            'wallet_address' => $wallet->address,
            'wallet_id' => $wallet->id,
            'amount' => $request->amount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
        ]);

        // Checa se tem algum depósito já batendo com essa carteira
        app(WalletController::class)->tryConfirmPendingDonation($wallet, $donation);

        return response()->json(['donation' => $donation]);
    }

    public function show($id)
    {
        $donation = Donation::with('user')->findOrFail($id);

        // Atualiza status se necessário (checando se houve depósito)
        if ($donation->status === 'pending') {
            $result = app(WalletController::class)->tryConfirmPendingDonation($donation->wallet, $donation);
        }

        // Recarrega a doação atualizada
        $donation->refresh();

        return response()->json(['donation' => $donation]);
    }


}
