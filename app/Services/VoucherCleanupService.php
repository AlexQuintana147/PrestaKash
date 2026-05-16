<?php

namespace App\Services;

use App\Models\InterestPayment;
use App\Models\LoanInstallment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VoucherCleanupService
{
    /**
     * @return array{from: string, to: string, count: int, total_bytes: int, total_human: string, items: array<int, array<string, mixed>>}
     */
    public function preview(Carbon $from, Carbon $to): array
    {
        $items = $this->collectVouchers($from, $to);
        $totalBytes = $items->unique('path')->sum('size_bytes');

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'count' => $items->count(),
            'total_bytes' => $totalBytes,
            'total_human' => $this->formatBytes($totalBytes),
            'items' => $items->values()->all(),
        ];
    }

    /**
     * @return array{deleted_count: int, freed_bytes: int, freed_human: string}
     */
    public function cleanup(Carbon $from, Carbon $to): array
    {
        $items = $this->collectVouchers($from, $to);
        $freedBytes = 0;
        $deletedCount = 0;

        DB::transaction(function () use ($items, &$freedBytes, &$deletedCount) {
            $deletedPaths = [];

            foreach ($items as $item) {
                $path = $item['path'];
                $size = $item['size_bytes'];

                if ($path && ! in_array($path, $deletedPaths, true) && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deletedPaths[] = $path;
                    $freedBytes += $size;
                }

                if ($item['source'] === 'installment') {
                    LoanInstallment::where('id', $item['source_id'])
                        ->where('payment_voucher', $path)
                        ->update(['payment_voucher' => null]);
                } else {
                    InterestPayment::where('id', $item['source_id'])
                        ->where('payment_voucher', $path)
                        ->update(['payment_voucher' => null]);
                }

                $deletedCount++;
            }
        });

        return [
            'deleted_count' => $deletedCount,
            'freed_bytes' => $freedBytes,
            'freed_human' => $this->formatBytes($freedBytes),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function collectVouchers(Carbon $from, Carbon $to): Collection
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $items = collect();

        $installments = LoanInstallment::query()
            ->where('status', 'approved')
            ->whereNotNull('payment_voucher')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->with(['loan.user'])
            ->orderBy('paid_at')
            ->get();

        foreach ($installments as $inst) {
            $items->push($this->mapInstallmentVoucher($inst));
        }

        $interestPayments = InterestPayment::query()
            ->whereNotNull('payment_voucher')
            ->whereBetween('paid_at', [$from, $to])
            ->with(['installment.loan.user'])
            ->orderBy('paid_at')
            ->get();

        $interestIndexByInstallment = [];

        foreach ($interestPayments as $payment) {
            $installmentId = $payment->loan_installment_id;
            $interestIndexByInstallment[$installmentId] = ($interestIndexByInstallment[$installmentId] ?? 0) + 1;
            $items->push($this->mapInterestVoucher($payment, $interestIndexByInstallment[$installmentId]));
        }

        return $items
            ->unique(fn (array $item) => $item['source'].'-'.$item['source_id'])
            ->sortBy('paid_at')
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function mapInstallmentVoucher(LoanInstallment $inst): array
    {
        $path = $inst->payment_voucher;
        $size = $this->fileSize($path);

        return [
            'source' => 'installment',
            'source_id' => $inst->id,
            'path' => $path,
            'url' => $path ? Storage::url($path) : null,
            'filename' => basename($path),
            'size_bytes' => $size,
            'size_human' => $this->formatBytes($size),
            'paid_at' => $inst->paid_at?->toIso8601String(),
            'paid_at_label' => $inst->paid_at?->format('d/m/Y H:i'),
            'client' => $inst->loan->user->name ?? '—',
            'loan_id' => $inst->loan_id,
            'label' => 'Cuota completa #'.$inst->installment_number,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapInterestVoucher(InterestPayment $payment, int $paymentNumber): array
    {
        $inst = $payment->installment;
        $path = $payment->payment_voucher;
        $size = $this->fileSize($path);

        return [
            'source' => 'interest_payment',
            'source_id' => $payment->id,
            'path' => $path,
            'url' => $path ? Storage::url($path) : null,
            'filename' => basename($path),
            'size_bytes' => $size,
            'size_human' => $this->formatBytes($size),
            'paid_at' => $payment->paid_at?->toIso8601String(),
            'paid_at_label' => $payment->paid_at?->format('d/m/Y H:i'),
            'client' => $inst?->loan?->user?->name ?? '—',
            'loan_id' => $inst?->loan_id,
            'label' => 'Solo interés #'.$paymentNumber.' (Cuota #'.($inst?->installment_number ?? '?').')',
        ];
    }

    private function fileSize(?string $path): int
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return 0;
        }

        return (int) Storage::disk('public')->size($path);
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 2).' MB';
    }
}
