<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Controllers\WalletController;

class CheckPendingDonations extends Command
{
    protected $signature = 'donations:check';
    protected $description = 'Check if pending donations have been paid';

    public function handle()
    {
        $pending = Donation::where('status', 'pending')->get();

        foreach ($pending as $donation) {
            if ($donation->wallet_address) {
                $result = app(WalletController::class)->checkDeposits($donation->wallet_address);

                // Dump para debug
                dump([
                    'donation_id' => $donation->id,
                    'wallet' => $donation->wallet_address,
                    'donation_amount' => $donation->amount,
                    'result' => $result,
                ]);

                if ($result['success'] && !empty($result['usdt_deposits'])) {
                    foreach ($result['usdt_deposits'] as $deposit) {
                        $decimals = $deposit['token_info']['decimals'] ?? 6;
                        $depositAmount = (float) $deposit['value'] / pow(10, $decimals);

                        $this->line("💰 Verificando depósito: {$depositAmount} USDT");

                        if (abs($depositAmount - (float) $donation->amount) < 0.01) {
                            $donation->status = 'completed';
                            $donation->expires_at = now();
                            $donation->tx_id = $deposit['transaction_id'] ?? null;
                            $donation->save();

                            $this->info("🎉 Donation #{$donation->id} expires automatically.");
                            break;
                        }
                    }
                }
            } else {
                $this->warn("⚠️ Donation #{$donation->id} has no wallet address.");
            }
        }

        return 0;
    }
}
