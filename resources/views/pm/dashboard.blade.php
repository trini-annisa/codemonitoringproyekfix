@extends('layouts.app')
@section('title', 'Dashboard PM')
@section('page-title', 'Dashboard Project Manager')
@section('page-subtitle', 'Monitor kinerja EVA proyek Anda')

@push('styles')
<style>
    .progress-ring { transition: stroke-dashoffset 0.8s ease-in-out; }
</style>
@endpush

@section('content')

{{-- SELECTOR PROYEK --}}
<div class="mb-6 flex items-center gap-3">
    <label class="text-sm font-semibold text-slate-600">Proyek:</label>
    <form method="GET">
        <select name="project_id" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-4 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @foreach($myProjects as $p)
                <option value="{{ $p->id }}" {{ $selectedProject?->id == $p->id ? 'selected' : '' }}>
                    {{ $p->project_code }} — {{ $p->name }}
                </option>
            @endforeach
        </select>
    </form>
</div>

@if($selectedProject)

{{-- KPI EVA CARDS --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @php
        $eva = $latestEva;
        $cpiColor = $eva ? ($eva->cpi >= 1 ? 'emerald' : ($eva->cpi >= 0.9 ? 'amber' : 'red')) : 'slate';
        $spiColor = $eva ? ($eva->spi >= 1 ? 'emerald' : ($eva->spi >= 0.9 ? 'amber' : 'red')) : 'slate';
    @endphp

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <p class="text-xs text-slate-400 mb-1 font-medium uppercase tracking-wide">BAC</p>
        <p class="text-xl font-bold text-slate-800">Rp {{ number_format($selectedProject->bac/1000000, 1) }}Jt</p>
        <p class="text-xs text-slate-400 mt-1">Budget at Completion</p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <p class="text-xs text-slate-400 mb-1 font-medium uppercase tracking-wide">CPI</p>
        <p class="text-xl font-bold text-{{ $cpiColor }}-600">{{ $eva ? number_format($eva->cpi, 2) : '—' }}</p>
        <p class="text-xs text-{{ $cpiColor }}-500 mt-1">
            {{ $eva ? ($eva->cpi >= 1 ? '✓ Under Budget' : ($eva->cpi >= 0.9 ? '~ On Budget' : '⚠ Over Budget')) : 'Belum ada data' }}
        </p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <p class="text-xs text-slate-400 mb-1 font-medium uppercase tracking-wide">SPI</p>
        <p class="text-xl font-bold text-{{ $spiColor }}-600">{{ $eva ? number_format($eva->spi, 2) : '—' }}</p>
        <p class="text-xs text-{{ $spiColor }}-500 mt-1">
            {{ $eva ? ($eva->spi >= 1 ? '✓ Ahead' : ($eva->spi >= 0.9 ? '~ On Schedule' : '⚠ Behind')) : 'Belum ada data' }}
        </p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <p class="text-xs text-slate-400 mb-1 font-medium uppercase tracking-wide">EAC</p>
        <p class="text-xl font-bold text-slate-800">{{ $eva ? 'Rp '.number_format($eva->eac/1000000, 1).'Jt' : '—' }}</p>
        <p class="text-xs text-slate-400 mt-1">Estimate at Completion</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">

    {{-- S-CURVE CHART --}}
    <div class="col-span-2 bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-semibold text-slate-700">S-Curve Progress</h2>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-blue-500 inline-block rounded"></span>Rencana (PV)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-emerald-500 inline-block rounded"></span>Realisasi (EV)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-red-400 inline-block rounded dashed"></span>Aktual (AC)</span>
            </div>
        </div>
        <canvas id="sCurveChart" height="220"></canvas>
    </div>

    {{-- PROGRESS RING --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 flex flex-col items-center justify-center">
        <h2 class="font-semibold text-slate-700 mb-5 self-start">Progress Fisik</h2>
        @php $pct = $eva?->physical_progress_percent ?? 0; @endphp
        <div class="relative w-36 h-36">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="50" fill="none" stroke="#f1f5f9" stroke-width="12"/>
                <circle cx="60" cy="60" r="50" fill="none"
                    stroke="{{ $pct >= 70 ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#3b82f6') }}"
                    stroke-width="12"
                    stroke-linecap="round"
                    stroke-dasharray="{{ 314 }}"
                    stroke-dashoffset="{{ 314 - (314 * $pct / 100) }}"
                    class="progress-ring"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold text-slate-800">{{ number_format($pct, 1) }}%</span>
                <span class="text-xs text-slate-400">Fisik</span>
            </div>
        </div>
        <div class="mt-5 w-full space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Rencana</span>
                <span class="font-semibold">{{ number_format($eva?->planned_progress_percent ?? 0, 1) }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $eva?->planned_progress_percent ?? 0 }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- EVA TABLE --}}
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
        <h2 class="font-semibold text-slate-700">Tabel Rekaman EVA</h2>
        <a href="{{ route('pm.eva.export.pdf', $selectedProject) }}" ...>
    Export PDF
</a>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50">
                @foreach(['Periode','PV','EV','AC','CV','SV','CPI','SPI','EAC','ETC','VAC','Status Biaya','Status Jadwal'] as $col)
                <th class="px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide text-{{ in_array($col,['Periode','Status Biaya','Status Jadwal']) ? 'left' : 'right' }}">{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($evaHistory as $rec)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-4 py-3 font-medium text-slate-700">{{ $rec->period_label }}</td>
                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($rec->planned_value/1000000,1) }}Jt</td>
                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($rec->earned_value/1000000,1) }}Jt</td>
                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($rec->actual_cost/1000000,1) }}Jt</td>
                <td class="px-4 py-3 text-right font-semibold {{ $rec->cost_variance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ number_format($rec->cost_variance/1000000,1) }}Jt
                </td>
                <td class="px-4 py-3 text-right font-semibold {{ $rec->schedule_variance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ number_format($rec->schedule_variance/1000000,1) }}Jt
                </td>
                <td class="px-4 py-3 text-right font-bold {{ $rec->cpi >= 1 ? 'text-emerald-600' : ($rec->cpi >= 0.9 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($rec->cpi,2) }}</td>
                <td class="px-4 py-3 text-right font-bold {{ $rec->spi >= 1 ? 'text-emerald-600' : ($rec->spi >= 0.9 ? 'text-amber-600' : 'text-red-500') }}">{{ number_format($rec->spi,2) }}</td>
                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($rec->eac/1000000,1) }}Jt</td>
                <td class="px-4 py-3 text-right text-slate-600">{{ number_format($rec->etc/1000000,1) }}Jt</td>
                <td class="px-4 py-3 text-right font-semibold {{ $rec->vac >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($rec->vac/1000000,1) }}Jt</td>
                <td class="px-4 py-3">
                    <span class="status-badge {{ $rec->status_cost === 'under_budget' ? 'bg-emerald-50 text-emerald-700' : ($rec->status_cost === 'on_budget' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                        {{ $rec->status_cost === 'under_budget' ? 'Under Budget' : ($rec->status_cost === 'on_budget' ? 'On Budget' : 'Over Budget') }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <span class="status-badge {{ $rec->status_schedule === 'ahead_of_schedule' ? 'bg-emerald-50 text-emerald-700' : ($rec->status_schedule === 'on_schedule' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                        {{ $rec->status_schedule === 'ahead_of_schedule' ? 'Ahead' : ($rec->status_schedule === 'on_schedule' ? 'On Schedule' : 'Behind') }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="13" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada data EVA untuk proyek ini</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@else
<div class="bg-white rounded-2xl border border-slate-100 p-16 text-center">
    <p class="text-slate-400">Anda belum memiliki proyek yang ditugaskan.</p>
    <a href="/pm/projects/create" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">+ Buat Proyek Baru</a>
</div>
@endif

@endsection

@push('scripts')
<script>
const evaData = @json($evaHistory ?? []);
if (evaData.length > 0 && document.getElementById('sCurveChart')) {
    const labels = evaData.map(r => r.period_label);
    const pv = evaData.map(r => parseFloat(r.planned_value)/1000000);
    const ev = evaData.map(r => parseFloat(r.earned_value)/1000000);
    const ac = evaData.map(r => parseFloat(r.actual_cost)/1000000);

    new Chart(document.getElementById('sCurveChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label: 'PV (Rencana)', data: pv, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.06)', tension: 0.4, borderWidth: 2.5, pointRadius: 4, fill: true },
                { label: 'EV (Realisasi)', data: ev, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.06)', tension: 0.4, borderWidth: 2.5, pointRadius: 4, fill: true },
                { label: 'AC (Biaya Aktual)', data: ac, borderColor: '#ef4444', backgroundColor: 'transparent', tension: 0.4, borderWidth: 2, borderDash: [5,3], pointRadius: 4 },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#f8fafc' }, ticks: { callback: v => 'Rp '+v+'Jt', font: { size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
}
</script>
@endpush
