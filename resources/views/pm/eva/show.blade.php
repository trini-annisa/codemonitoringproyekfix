@extends('layouts.app')
@section('title', 'Laporan EVA')
@section('page-title', 'Laporan Earned Value Analysis')
@section('page-subtitle', $project->name . ' — ' . $project->project_code)

@section('content')

{{-- Header Info Proyek --}}
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">{{ $project->name }}</h2>
            <p class="text-sm text-slate-500 font-mono mt-0.5">{{ $project->project_code }}</p>
            <div class="flex gap-4 mt-3 text-sm text-slate-600">
                <span><span class="text-slate-400">Lokasi:</span> {{ $project->location ?? '—' }}</span>
                <span><span class="text-slate-400">Kontrak:</span> {{ $project->contract_number ?? '—' }}</span>
                <span><span class="text-slate-400">Periode:</span>
                    {{ $project->start_date->format('d/m/Y') }} —
                    {{ $project->end_date->format('d/m/Y') }}
                </span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400">BAC (Budget at Completion)</p>
            <p class="text-xl font-bold text-slate-800">
                Rp {{ number_format($project->bac, 0, ',', '.') }}
            </p>
        </div>
    </div>
</div>

@if($evaRecords->isEmpty())
<div class="bg-white rounded-2xl border border-slate-100 p-16 text-center text-slate-400">
    <p class="text-lg font-medium mb-2">Belum ada data EVA</p>
    <p class="text-sm">Silakan input progress pekerjaan terlebih dahulu.</p>
    <a href="{{ route('pm.progress.create') }}?project_id={{ $project->id }}"
       class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold">
        Input Progress →
    </a>
</div>
@else

{{-- Periode Terbaru --}}
@php $latest = $evaRecords->last(); @endphp

{{-- KPI Cards Periode Terbaru --}}
<div class="mb-2 flex items-center justify-between">
    <p class="text-sm font-semibold text-slate-600">
        Periode Terbaru:
        <span class="font-mono text-blue-600">{{ $latest->period_label }}</span>
        — {{ $latest->report_date->format('d M Y') }}
    </p>
    <div class="flex gap-2">
        <a href="{{ route('pm.eva.export.pdf', $project) }}"
           class="bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-red-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export PDF
        </a>
        <a href="{{ route('pm.eva.export.excel', $project) }}"
           class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-emerald-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
    </div>
</div>

{{-- 10 KPI Cards --}}
<div class="grid grid-cols-5 gap-3 mb-6">
    @php
        $kpis = [
            ['label' => 'Planned Value (PV)',   'value' => 'Rp '.number_format($latest->planned_value,0,',','.'),
             'color' => 'blue',   'sub' => 'Nilai rencana kumulatif'],
            ['label' => 'Earned Value (EV)',    'value' => 'Rp '.number_format($latest->earned_value,0,',','.'),
             'color' => 'emerald','sub' => 'Nilai pekerjaan selesai'],
            ['label' => 'Actual Cost (AC)',     'value' => 'Rp '.number_format($latest->actual_cost,0,',','.'),
             'color' => 'amber',  'sub' => 'Biaya aktual dikeluarkan'],
            ['label' => 'Cost Variance (CV)',   'value' => 'Rp '.number_format($latest->cost_variance,0,',','.'),
             'color' => $latest->cost_variance >= 0 ? 'emerald' : 'red',
             'sub'   => $latest->cost_variance >= 0 ? 'Under Budget' : 'Cost Overrun'],
            ['label' => 'Schedule Variance (SV)','value'=> 'Rp '.number_format($latest->schedule_variance,0,',','.'),
             'color' => $latest->schedule_variance >= 0 ? 'emerald' : 'red',
             'sub'   => $latest->schedule_variance >= 0 ? 'On Schedule' : 'Behind Schedule'],
            ['label' => 'CPI',                  'value' => number_format($latest->cpi,4),
             'color' => $latest->cpi >= 1 ? 'emerald' : ($latest->cpi >= 0.95 ? 'amber' : 'red'),
             'sub'   => $latest->cpi >= 1 ? 'Under Budget' : 'Over Budget'],
            ['label' => 'SPI',                  'value' => number_format($latest->spi,4),
             'color' => $latest->spi >= 1 ? 'emerald' : ($latest->spi >= 0.95 ? 'amber' : 'red'),
             'sub'   => $latest->spi >= 1 ? 'On Schedule' : 'Behind Schedule'],
            ['label' => 'EAC',                  'value' => 'Rp '.number_format($latest->eac,0,',','.'),
             'color' => 'slate',  'sub' => 'Estimate at Completion'],
            ['label' => 'ETC',                  'value' => 'Rp '.number_format($latest->etc,0,',','.'),
             'color' => 'slate',  'sub' => 'Estimate to Complete'],
            ['label' => 'VAC',                  'value' => 'Rp '.number_format($latest->vac,0,',','.'),
             'color' => $latest->vac >= 0 ? 'emerald' : 'red',
             'sub'   => $latest->vac >= 0 ? 'Surplus' : 'Defisit'],
        ];
    @endphp

    @foreach($kpis as $kpi)
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-400 font-medium mb-1">{{ $kpi['label'] }}</p>
        <p class="text-sm font-bold text-{{ $kpi['color'] }}-600 mb-0.5">{{ $kpi['value'] }}</p>
        <p class="text-xs text-slate-400">{{ $kpi['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Grafik S-Curve --}}
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6">
    <h3 class="font-semibold text-slate-700 mb-4">Grafik S-Curve (PV / EV / AC)</h3>
    <canvas id="sCurve" height="80"></canvas>
</div>

{{-- Tabel Semua Periode --}}
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-50">
        <h3 class="font-semibold text-slate-700">Rekap EVA Semua Periode</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Periode</th>
                    <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Tanggal</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">PV</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">EV</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">AC</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">SV</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">CV</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">SPI</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">CPI</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">EAC</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">ETC</th>
                    <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">VAC</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($evaRecords as $rec)
                <tr class="hover:bg-slate-50/50 {{ $rec->id === $latest->id ? 'bg-blue-50/30' : '' }}">
                    <td class="px-4 py-3 font-mono font-semibold text-slate-700">
                        {{ $rec->period_label }}
                        @if($rec->id === $latest->id)
                        <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded ml-1">Terbaru</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $rec->report_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right text-slate-600 text-xs">{{ number_format($rec->planned_value/1000000,2) }}Jt</td>
                    <td class="px-4 py-3 text-right text-emerald-600 text-xs font-medium">{{ number_format($rec->earned_value/1000000,2) }}Jt</td>
                    <td class="px-4 py-3 text-right text-amber-600 text-xs font-medium">{{ number_format($rec->actual_cost/1000000,2) }}Jt</td>
                    <td class="px-4 py-3 text-right text-xs font-medium {{ $rec->schedule_variance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $rec->schedule_variance >= 0 ? '+' : '' }}{{ number_format($rec->schedule_variance/1000000,2) }}Jt
                    </td>
                    <td class="px-4 py-3 text-right text-xs font-medium {{ $rec->cost_variance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $rec->cost_variance >= 0 ? '+' : '' }}{{ number_format($rec->cost_variance/1000000,2) }}Jt
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-bold {{ $rec->spi >= 1 ? 'text-emerald-600' : ($rec->spi >= 0.95 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ number_format($rec->spi,4) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-bold {{ $rec->cpi >= 1 ? 'text-emerald-600' : ($rec->cpi >= 0.95 ? 'text-amber-500' : 'text-red-500') }}">
                            {{ number_format($rec->cpi,4) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-xs text-slate-600">{{ number_format($rec->eac/1000000,2) }}Jt</td>
                    <td class="px-4 py-3 text-right text-xs text-slate-600">{{ number_format($rec->etc/1000000,2) }}Jt</td>
                    <td class="px-4 py-3 text-right text-xs font-medium {{ $rec->vac >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $rec->vac >= 0 ? '+' : '' }}{{ number_format($rec->vac/1000000,2) }}Jt
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $sc = $rec->status_cost;
                            $ss = $rec->status_schedule;
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ $sc === 'under_budget' ? 'bg-emerald-100 text-emerald-700' :
                               ($sc === 'on_budget' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                            {{ $sc === 'under_budget' ? 'Under' : ($sc === 'on_budget' ? 'On Budget' : 'Overrun') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Interpretasi Kuadran CPI-SPI --}}
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h3 class="font-semibold text-slate-700 mb-4">Interpretasi Kinerja Proyek (Matriks CPI-SPI)</h3>
    @php
        $cpi = $latest->cpi;
        $spi = $latest->spi;
        if ($cpi > 1 && $spi > 1)      { $zone = 'I';   $zoneColor = 'emerald'; $zoneDesc = 'Ahead of Schedule & Under Budget — Kondisi Ideal'; }
        elseif ($cpi < 1 && $spi > 1)  { $zone = 'II';  $zoneColor = 'amber';   $zoneDesc = 'Ahead of Schedule namun Over Budget'; }
        elseif ($cpi > 1 && $spi < 1)  { $zone = 'III'; $zoneColor = 'amber';   $zoneDesc = 'Behind Schedule namun Under Budget'; }
        else                            { $zone = 'IV';  $zoneColor = 'red';     $zoneDesc = 'Behind Schedule & Over Budget — Kondisi Kritis'; }
    @endphp
    <div class="flex items-center gap-4 p-4 bg-{{ $zoneColor }}-50 border border-{{ $zoneColor }}-200 rounded-xl">
        <div class="w-12 h-12 bg-{{ $zoneColor }}-100 rounded-full flex items-center justify-center">
            <span class="text-{{ $zoneColor }}-700 font-bold text-lg">{{ $zone }}</span>
        </div>
        <div>
            <p class="font-semibold text-{{ $zoneColor }}-800">Zone {{ $zone }}: {{ $zoneDesc }}</p>
            <p class="text-sm text-{{ $zoneColor }}-600 mt-0.5">
                CPI = {{ number_format($cpi,4) }} &nbsp;|&nbsp;
                SPI = {{ number_format($spi,4) }} &nbsp;|&nbsp;
                TCPI = {{ number_format($latest->tcpi,4) }}
            </p>
        </div>
    </div>
</div>

@endif

@endsection

@push('scripts')
@if(!$evaRecords->isEmpty())
<script>
const labels = @json($evaRecords->pluck('period_label'));
const pvData  = @json($evaRecords->pluck('planned_value'));
const evData  = @json($evaRecords->pluck('earned_value'));
const acData  = @json($evaRecords->pluck('actual_cost'));

new Chart(document.getElementById('sCurve'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'PV (Planned Value)',
                data: pvData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true, tension: 0.4, borderWidth: 2,
                pointBackgroundColor: '#2563eb',
            },
            {
                label: 'EV (Earned Value)',
                data: evData,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.08)',
                fill: true, tension: 0.4, borderWidth: 2,
                pointBackgroundColor: '#16a34a',
            },
            {
                label: 'AC (Actual Cost)',
                data: acData,
                borderColor: '#dc2626',
                backgroundColor: 'transparent',
                fill: false, tension: 0.4, borderWidth: 2,
                borderDash: [5,5],
                pointBackgroundColor: '#dc2626',
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': Rp ' +
                        Number(ctx.parsed.y).toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                ticks: {
                    callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'Jt'
                },
                grid: { color: 'rgba(0,0,0,0.04)' }
            }
        }
    }
});
</script>
@endif
@endpush
