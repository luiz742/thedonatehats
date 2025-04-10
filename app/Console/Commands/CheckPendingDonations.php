<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use Carbon\Carbon;

class CheckPendingDonations extends Command
{
    protected $signature = 'donations:check';
    protected $description = 'Check if pending donations have been paid or expired';

    public function handle()
    {
        $pending = Donation::where('status', 'pending')->get();
        $now = Carbon::now();

        foreach ($pending as $donation) {
            $createdAt = Carbon::parse($donation->created_at);
            $diff = $createdAt->diffInMinutes($now); // sempre positivo

            if ($diff > 15) {
                $donation->status = 'expired';
                $donation->save();
                $this->info("Donation #{$donation->id} marked as expired. ({$diff} min old)");
            } else {
                $this->info("Donation #{$donation->id} ainda dentro do prazo. ({$diff} min old)");
            }
        }

        return 0;
    }
}
