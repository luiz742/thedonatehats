<?php

// app/Http/Controllers/WalletController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use StephenHill\Base58;
use Illuminate\Support\Facades\Http;
use App\Models\Wallet;
use App\Models\Donation;

class WalletController extends Controller
{
    // WalletController.php

    public function tryConfirmPendingDonation(Wallet $wallet, Donation $donation)
    {
        $result = $this->checkDeposits($wallet->address);

        if ($result['success'] && !empty($result['usdt_deposits'])) {
            foreach ($result['usdt_deposits'] as $deposit) {
                // Verifica se o valor bate com a doação
                if ((float) $deposit['amount'] == (float) $donation->amount) {
                    // Atualiza a doação como confirmada
                    $donation->status = 'completed';
                    $donation->confirmed_at = now();
                    $donation->tx_id = $deposit['tx_id'] ?? null;
                    $donation->save();

                    return true;
                }
            }
        }

        return false;
    }

    public function generate()
    {
        $existingWallet = Wallet::where('user_id', Auth::id())->first();

        if ($existingWallet) {
            return back()->withErrors(['Você já possui uma carteira.']);
        }

        $pathToScript = base_path('tronweb/generate_wallet.js');
        $command = "node " . escapeshellarg($pathToScript);
        $output = shell_exec($command);

        if (!$output) {
            return back()->withErrors(['Erro: o script não retornou nada.']);
        }

        $walletData = json_decode($output);

        if (!$walletData || !isset($walletData->address)) {
            return back()->withErrors(['Erro: dados da carteira inválidos -> ' . $output]);
        }

        Wallet::create([
            'user_id' => Auth::id(),
            'address' => $walletData->address,
            'private_key' => Crypt::encryptString($walletData->privateKey),
            'public_key' => $walletData->publicKey,
        ]);

        return redirect()->back()->with('success', 'Carteira criada com sucesso!');
    }

    private function hexToBase58($hexAddress)
    {
        $addressBytes = hex2bin($hexAddress);
        $checksum = substr(hash('sha256', hash('sha256', $addressBytes, true), true), 0, 4);
        $addressWithChecksum = $addressBytes . $checksum;

        $base58 = new Base58();
        return $base58->encode($addressWithChecksum);
    }

    public function checkDeposits($address)
    {
        $usdtContractAddress = 'a614f803b6fd780986a42c78ec9c7f77e6ded13c';
        $url = "https://api.shasta.trongrid.io/v1/accounts/{$address}/transactions/trc20?limit=50";

        try {
            $response = Http::acceptJson()->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Erro ao buscar transações TRC20',
                ];
            }

            $transactions = collect($response->json('data') ?? []);

            // Filtrar apenas USDT recebidos com validação de campos
            $incomingUSDT = $transactions->filter(function ($tx) use ($address, $usdtContractAddress) {
                return (
                    isset($tx['to'], $tx['token_info']['address']) &&
                    strtolower($tx['to']) === strtolower($address) &&
                    strtolower($tx['token_info']['address']) === strtolower($usdtContractAddress)
                );
            })->map(function ($tx) {
                return [
                    'tx_id' => $tx['transaction_id'] ?? null,
                    'from' => $tx['from'] ?? null,
                    'to' => $tx['to'] ?? null,
                    'amount' => isset($tx['value']) ? $tx['value'] / pow(10, 6) : 0,
                    'timestamp' => isset($tx['block_timestamp']) ? date('Y-m-d H:i:s', $tx['block_timestamp'] / 1000) : null,
                ];
            });

            return [
                'success' => true,
                'usdt_deposits' => $transactions,
                // 'usdt_deposits' => $incomingUSDT->values(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar depósitos USDT: ' . $e->getMessage(),
            ];
        }
    }


    private function getIncomingDeposits($address)
    {
        $url = "https://api.shasta.trongrid.io/v1/accounts/{$address}/transactions?limit=50";

        try {
            $response = Http::acceptJson()->get($url);

            if (!$response->successful())
                return [];

            $transactions = $response->json('data') ?? [];

            return collect($transactions)->filter(function ($tx) use ($address) {
                $contract = $tx['raw_data']['contract'][0]['parameter']['value'] ?? null;

                if (!$contract || !isset($contract['to_address']) || !isset($contract['amount'])) {
                    return false;
                }

                $to = $this->hexToBase58($contract['to_address']);
                return strtoupper($to) === strtoupper($address);
            })->map(function ($tx) {
                $value = $tx['raw_data']['contract'][0]['parameter']['value'];

                return [
                    'tx_id' => $tx['txID'],
                    'from' => $this->hexToBase58($value['owner_address']),
                    'to' => $this->hexToBase58($value['to_address']),
                    'amount' => $value['amount'] / 1_000_000, // USDT ou TRX vem em 6 casas
                    'timestamp' => date('Y-m-d H:i:s', $tx['block_timestamp'] / 1000),
                ];
            })->values()->all();

        } catch (\Exception $e) {
            return [];
        }

    }

    public function getIncomingDepositsFormatted($address)
    {
        $rawTxs = $this->getIncomingDeposits($address);

        return collect($rawTxs)->map(function ($tx) {
            return [
                'tx_id' => $tx['tx_id'] ?? '',
                'from' => $tx['from'] ?? '',
                'amount' => $tx['amount'] ?? 0,
                'timestamp' => $tx['timestamp'] ?? now()->timestamp,
            ];
        })->values();
    }

    // public function index()
    // {
    //     $wallet = Wallet::where('user_id', Auth::id())->first();
    //     $incomingTransactions = [];

    //     if ($wallet) {
    //         $incomingTransactions = $this->getIncomingDeposits($wallet->address);
    //     }

    //     return inertia('Wallet/Index', [
    //         'wallet' => $wallet,
    //         'deposits' => $incomingTransactions,
    //     ]);
    // }
}
