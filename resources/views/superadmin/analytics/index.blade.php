@extends('superadmin.layout')

@section('title', 'Análisis')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
    }
    .dark .stat-card:hover {
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.4);
    }
    .chart-container {
        position: relative;
        width: 100%;
    }
    .fade-in {
        animation: fadeIn 0.5s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeIn {
        to { opacity: 1; }
    }
    .fade-in:nth-child(1) { animation-delay: 0.05s; }
    .fade-in:nth-child(2) { animation-delay: 0.1s; }
    .fade-in:nth-child(3) { animation-delay: 0.15s; }
    .fade-in:nth-child(4) { animation-delay: 0.2s; }
    .fade-in:nth-child(5) { animation-delay: 0.25s; }
    .fade-in:nth-child(6) { animation-delay: 0.3s; }
    .fade-in:nth-child(7) { animation-delay: 0.35s; }
    .fade-in:nth-child(8) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Análisis</h1>
            <p class="text-sm text-gray-500 dark:text-gray-100 mt-0.5">Métricas y estadísticas del sistema</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                En vivo
            </span>
            <button onclick="location.reload()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-100 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Actualizar
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="stat-card fade-in bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                @if($loanStats['disbursedChange'] != 0)
                    <span class="text-xs font-medium {{ $loanStats['disbursedChange'] > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $loanStats['disbursedChange'] > 0 ? '+' : '' }}{{ $loanStats['disbursedChange'] }}%
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-100 mb-1">Desembolsado mes</p>
            <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($loanStats['disbursedThisMonth'], 0) }}</p>
        </div>

        <div class="stat-card fade-in bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-xs font-medium text-green-600 dark:text-green-400">{{ $collectionStats['collectionRate'] }}%</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-100 mb-1">Cobrado mes</p>
            <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($collectionStats['totalCollected'], 0) }}</p>
        </div>

        <div class="stat-card fade-in bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-100 mb-1">Caja total</p>
            <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">S/ {{ number_format($cashboxStats['totalBalance'], 0) }}</p>
        </div>

        <div class="stat-card fade-in bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-100 mb-1">Inversores activos</p>
            <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $investmentStats['activeInvestors'] }}</p>
        </div>
    </div>

    {{-- Secondary KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">Préstamos activos</p>
            <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $loanStats['activeLoans'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">Pendientes</p>
            <p class="text-base font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $loanStats['pendingLoans'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">En mora</p>
            <p class="text-base font-bold text-red-600 dark:text-red-400 mt-1">{{ $loanStats['defaultedLoans'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">Cuotas vencidas</p>
            <p class="text-base font-bold text-red-600 dark:text-red-400 mt-1">{{ $collectionStats['overdueInstallments'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">Pagos procesando</p>
            <p class="text-base font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $collectionStats['processingPayments'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-3.5 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-100">Tasa interés prom.</p>
            <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ number_format($loanStats['avgInterestRate'], 1) }}%</p>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        {{-- Monthly Trend --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tendencia mensual</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Desembolsos vs cobranzas (6 meses)</p>
                </div>
            </div>
            <div class="chart-container" style="height: 280px;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        {{-- Loan Status Distribution --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Distribución de préstamos</h3>
                <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Por estado actual</p>
            </div>
            <div class="chart-container flex items-center justify-center" style="height: 220px;">
                <canvas id="loanStatusChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($loanStatusDistribution as $status => $count)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ \Illuminate\Support\Arr::get(['approved' => 'bg-green-500', 'pending' => 'bg-amber-500', 'rejected' => 'bg-red-500', 'counter_offered' => 'bg-blue-500', 'disbursed' => 'bg-purple-500'], $status, 'bg-gray-400') }}"></span>
                            <span class="text-gray-600 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        </div>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        {{-- Revenue Breakdown --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Ingresos por concepto</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Intereses, moras y comisiones</p>
                </div>
            </div>
            <div class="chart-container" style="height: 260px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Payment Timeliness --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Puntualidad de pagos</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Distribución del mes actual</p>
                </div>
            </div>
            <div class="chart-container flex items-center justify-center" style="height: 200px;">
                <canvas id="timelinessChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-3 gap-3">
                <div class="text-center">
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ $paymentTimeliness['onTimePercent'] }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-100">A tiempo</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $paymentTimeliness['gracePercent'] }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-100">En gracia</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ $paymentTimeliness['latePercent'] }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-100">Con mora</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        {{-- Cashbox Flow --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Flujo de caja</h3>
                <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Ingresos vs egresos del mes</p>
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                    <p class="text-xs text-green-600 dark:text-green-400">Ingresos</p>
                    <p class="text-sm font-bold text-green-700 dark:text-green-300 mt-0.5">S/ {{ number_format($cashboxStats['incomeThisMonth'], 0) }}</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center">
                    <p class="text-xs text-red-600 dark:text-red-400">Egresos</p>
                    <p class="text-sm font-bold text-red-700 dark:text-red-300 mt-0.5">S/ {{ number_format($cashboxStats['expenseThisMonth'], 0) }}</p>
                </div>
                <div class="{{ $cashboxStats['netFlow'] >= 0 ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-amber-50 dark:bg-amber-900/20' }} rounded-lg p-3 text-center">
                    <p class="text-xs {{ $cashboxStats['netFlow'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400' }}">Neto</p>
                    <p class="text-sm font-bold {{ $cashboxStats['netFlow'] >= 0 ? 'text-blue-700 dark:text-blue-300' : 'text-amber-700 dark:text-amber-300' }} mt-0.5">S/ {{ number_format($cashboxStats['netFlow'], 0) }}</p>
                </div>
            </div>
            <div class="chart-container" style="height: 200px;">
                <canvas id="cashboxFlowChart"></canvas>
            </div>
        </div>

        {{-- Investment Performance --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Rendimiento de inversiones</h3>
                <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Progreso de oportunidades activas</p>
            </div>
            <div class="space-y-3">
                @forelse($investmentPerformance as $inv)
                    <div class="group">
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[60%]">{{ $inv['title'] }}</p>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-100">{{ $inv['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 ease-out {{ $inv['progress'] >= 75 ? 'bg-green-500' : ($inv['progress'] >= 50 ? 'bg-blue-500' : ($inv['progress'] >= 25 ? 'bg-amber-500' : 'bg-red-500')) }}"
                                 style="width: {{ $inv['progress'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[10px] text-gray-400">S/ {{ number_format($inv['raised'], 0) }} recaudado</span>
                            <span class="text-[10px] text-gray-400">Retorno {{ number_format($inv['returnRate'], 1) }}% mensual</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-4">Sin oportunidades registradas</p>
                @endforelse
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 grid grid-cols-2 gap-3">
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-100">Total recaudado</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">S/ {{ number_format($investmentStats['totalRaised'], 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-100">Pagos pendientes</p>
                    <p class="text-sm font-bold text-amber-600 dark:text-amber-400 mt-0.5">S/ {{ number_format($investmentStats['pendingPayouts'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Debtors --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 md:p-6">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Principales deudores en mora</h3>
            <p class="text-xs text-gray-500 dark:text-gray-100 mt-0.5">Top 5 préstamos con mayor deuda vencida</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="text-left py-2.5 px-3 font-medium text-gray-500 dark:text-gray-100">Deudor</th>
                        <th class="text-left py-2.5 px-3 font-medium text-gray-500 dark:text-gray-100">Código</th>
                        <th class="text-right py-2.5 px-3 font-medium text-gray-500 dark:text-gray-100">Monto préstamo</th>
                        <th class="text-right py-2.5 px-3 font-medium text-gray-500 dark:text-gray-100">Deuda vencida</th>
                        <th class="text-right py-2.5 px-3 font-medium text-gray-500 dark:text-gray-100">% Vencido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDebtors as $debtor)
                        <tr class="border-b border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="py-2.5 px-3 font-medium text-gray-900 dark:text-white">{{ $debtor['name'] }}</td>
                            <td class="py-2.5 px-3 text-gray-500 dark:text-gray-100 font-mono">{{ $debtor['code'] }}</td>
                            <td class="py-2.5 px-3 text-right text-gray-600 dark:text-gray-100">S/ {{ number_format($debtor['total'], 0) }}</td>
                            <td class="py-2.5 px-3 text-right font-medium text-red-600 dark:text-red-400">S/ {{ number_format($debtor['amount'], 0) }}</td>
                            <td class="py-2.5 px-3 text-right">
                                @php $pct = $debtor['total'] > 0 ? round(($debtor['amount'] / $debtor['total']) * 100, 1) : 0; @endphp
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $pct > 50 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400">Sin deudores en mora</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#9ca3af' : '#6b7280';

    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = textColor;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;
    Chart.defaults.plugins.tooltip.backgroundColor = isDark ? '#1f2937' : '#ffffff';
    Chart.defaults.plugins.tooltip.titleColor = isDark ? '#f9fafb' : '#111827';
    Chart.defaults.plugins.tooltip.bodyColor = isDark ? '#d1d5db' : '#4b5563';
    Chart.defaults.plugins.tooltip.borderColor = isDark ? '#374151' : '#e5e7eb';
    Chart.defaults.plugins.tooltip.borderWidth = 1;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.padding = 10;

    const monthlyTrendData = @json($monthlyTrend);
    const loanStatusData = @json($loanStatusDistribution);
    const revenueData = @json($revenueByMonth);
    const timelinessData = @json($paymentTimeliness);

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'bar',
        data: {
            labels: monthlyTrendData.map(d => d.label),
            datasets: [
                {
                    label: 'Desembolsado',
                    data: monthlyTrendData.map(d => d.disbursed),
                    backgroundColor: isDark ? 'rgba(96,165,250,0.7)' : 'rgba(59,130,246,0.7)',
                    borderColor: isDark ? '#60a5fa' : '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: 'Cobrado',
                    data: monthlyTrendData.map(d => d.collected),
                    backgroundColor: isDark ? 'rgba(52,211,153,0.7)' : 'rgba(16,185,129,0.7)',
                    borderColor: isDark ? '#34d399' : '#10b981',
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', align: 'end' },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: S/ ${ctx.parsed.y.toLocaleString('es-PE')}`
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: {
                    grid: { color: gridColor },
                    border: { display: false },
                    ticks: {
                        callback: v => v >= 1000 ? `S/ ${(v/1000).toFixed(0)}k` : `S/ ${v}`
                    }
                }
            }
        }
    });

    const statusColors = {
        approved: isDark ? '#34d399' : '#10b981',
        pending: isDark ? '#fbbf24' : '#f59e0b',
        rejected: isDark ? '#f87171' : '#ef4444',
        counter_offered: isDark ? '#60a5fa' : '#3b82f6',
        disbursed: isDark ? '#a78bfa' : '#8b5cf6',
    };
    const statusLabels = Object.keys(loanStatusData).map(s => s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
    const statusValues = Object.values(loanStatusData);
    const statusColorValues = Object.keys(loanStatusData).map(s => statusColors[s] || (isDark ? '#6b7280' : '#9ca3af'));

    new Chart(document.getElementById('loanStatusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusColorValues,
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueData.map(d => d.label),
            datasets: [
                {
                    label: 'Intereses',
                    data: revenueData.map(d => d.interest),
                    backgroundColor: isDark ? 'rgba(96,165,250,0.7)' : 'rgba(59,130,246,0.7)',
                    borderRadius: 4,
                    barPercentage: 0.7,
                },
                {
                    label: 'Moras',
                    data: revenueData.map(d => d.lateFees),
                    backgroundColor: isDark ? 'rgba(251,191,36,0.7)' : 'rgba(245,158,11,0.7)',
                    borderRadius: 4,
                    barPercentage: 0.7,
                },
                {
                    label: 'Com. tecnológica',
                    data: revenueData.map(d => d.techFees),
                    backgroundColor: isDark ? 'rgba(167,139,250,0.7)' : 'rgba(139,92,246,0.7)',
                    borderRadius: 4,
                    barPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', align: 'end' },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: S/ ${ctx.parsed.y.toLocaleString('es-PE')}`
                    }
                }
            },
            scales: {
                x: { stacked: true, grid: { display: false }, border: { display: false } },
                y: {
                    stacked: true,
                    grid: { color: gridColor },
                    border: { display: false },
                    ticks: {
                        callback: v => v >= 1000 ? `S/ ${(v/1000).toFixed(0)}k` : `S/ ${v}`
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('timelinessChart'), {
        type: 'doughnut',
        data: {
            labels: ['A tiempo', 'En gracia', 'Con mora'],
            datasets: [{
                data: [timelinessData.onTime, timelinessData.grace, timelinessData.late],
                backgroundColor: [
                    isDark ? '#34d399' : '#10b981',
                    isDark ? '#60a5fa' : '#3b82f6',
                    isDark ? '#f87171' : '#ef4444',
                ],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                            return `${ctx.label}: ${ctx.parsed} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    const cashboxData = @json($cashboxStats);
    new Chart(document.getElementById('cashboxFlowChart'), {
        type: 'bar',
        data: {
            labels: ['Ingresos', 'Egresos', 'Flujo neto'],
            datasets: [{
                data: [cashboxData.incomeThisMonth, cashboxData.expenseThisMonth, cashboxData.netFlow],
                backgroundColor: [
                    isDark ? 'rgba(52,211,153,0.7)' : 'rgba(16,185,129,0.7)',
                    isDark ? 'rgba(248,113,113,0.7)' : 'rgba(239,68,68,0.7)',
                    cashboxData.netFlow >= 0
                        ? (isDark ? 'rgba(96,165,250,0.7)' : 'rgba(59,130,246,0.7)')
                        : (isDark ? 'rgba(251,191,36,0.7)' : 'rgba(245,158,11,0.7)')
                ],
                borderRadius: 6,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `S/ ${ctx.parsed.x.toLocaleString('es-PE')}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    border: { display: false },
                    ticks: {
                        callback: v => v >= 1000 ? `S/ ${(v/1000).toFixed(0)}k` : `S/ ${v}`
                    }
                },
                y: { grid: { display: false }, border: { display: false } }
            }
        }
    });
});
</script>
@endpush
