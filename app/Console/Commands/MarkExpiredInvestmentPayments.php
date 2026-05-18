<?php

namespace App\Console\Commands;

use App\Models\InvestmentOpportunity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MarkExpiredInvestmentPayments extends Command
{
    protected $signature = 'investments:mark-expired-payments';

    protected $description = 'Marca como expirados los pagos de inversores que no subieron comprobante en 24h';

    public function handle()
    {
        $expired = DB::table('investor_opportunity')
            ->where('status', 'approved')
            ->where('payment_status', 'pending')
            ->whereNull('payment_submitted_at')
            ->where('approved_at', '<', Carbon::now()->subDay())
            ->get();

        $count = 0;
        foreach ($expired as $record) {
            DB::table('investor_opportunity')
                ->where('id', $record->id)
                ->update(['payment_status' => 'expired']);
            $count++;
        }

        $this->info("{$count} pagos marcados como expirados.");
    }
}
