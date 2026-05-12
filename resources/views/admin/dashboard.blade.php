@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Pengawas')
@section('page-subtitle', 'Monitoring kinerja seluruh proyek konstruksi')

@section('content')

{{-- KPI CARDS --}}
<div class="grid grid-cols-4 gap-5 mb-8">
    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
            </div>
            <span class="status-badge bg-blue-50 text-blue-700">Total</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $totalProjects }}</p>
        <p class="text-sm text-slate-400 mt-1">Total Proyek</p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="status-badge bg-emerald-50 text-emerald-700">Aktif</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $activeProjects }}</p>
        <p class="text-sm text-slate-400 mt-1">Proyek Aktif</p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
            </div>
            <span class="status-badge bg-violet-50 text-violet-700">User</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ $totalPM }}</p>
        <p class="text-sm text-slate-400 mt-1">Project Manager</p>
    </div>

    <div class="kpi-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="status-badge bg-amber-50 text-amber-700">Nilai</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($totalContractValue/1000000000, 1) }}M</p>
        <p class="text-sm text-slate-400 mt-1">Total Nilai Kontrak</p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">

    {{-- DAFTAR PROYEK --}}
    <div class="col-span-2 bg-white rounded-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">Daftar Proyek</h2>
            <a href="/admin/projects" class="text-xs text-blue-600 hover:underline font-medium">Lihat Semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left px-6 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Proyek</th>
                    <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">PM</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">CPI</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">SPI</th>
                    <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($projects as $project)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-slate-700">{{ Str::limit($project->name, 35) }}</p>
                        <p class="text-xs text-slate-400">{{ $project->project_code }} · {{ $project->location }}</p>
                    </td>
                    <td class="px-4 py-4 text-slate-600">{{ $project->pm?->name ?? '-' }}</td>
                    <td class="px-4 py-4 text-center">
                        @php $latestEva = $project->evaRecords->last(); @endphp
                        @if($latestEva)
                            <span class="font-bold {{ $latestEva->cpi >= 1 ? 'text-emerald-600' : ($latestEva->cpi >= 0.9 ? 'text-amber-600' : 'text-red-500') }}">
                                {{ number_format($latestEva->cpi, 2) }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        @if($latestEva)
                            <span class="font-bold {{ $latestEva->spi >= 1 ? 'text-emerald-600' : ($latestEva->spi >= 0.9 ? 'text-amber-600' : 'text-red-500') }}">
                                {{ number_format($latestEva->spi, 2) }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="status-badge {{ $project->status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($project->status === 'completed' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') }}">
                            {{ ucfirst($project->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada proyek</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- LOG AKTIVITAS --}}
    <div class="bg-white rounded-2xl border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">Log Aktivitas</h2>
            <a href="/admin/logs" class="text-xs text-blue-600 hover:underline font-medium">Semua →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentLogs as $log)
            <div class="px-5 py-3">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 mt-0.5 text-xs font-bold text-slate-600">
                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-700">{{ $log->user?->name ?? 'System' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $log->description }}</p>
                        <p class="text-xs text-slate-300 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada aktivitas</div>
            @endforelse
        </div>
    </div>
</div>

{{-- RINGKASAN EVA GLOBAL --}}
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-slate-700">Ringkasan EVA Periode Terakhir</h2>
    </div>
    <div class="grid grid-cols-4 gap-4">
        @foreach(['CPI Rata-rata' => 'avg_cpi', 'SPI Rata-rata' => 'avg_spi', 'Cost Variance' => 'total_cv', 'Schedule Variance' => 'total_sv'] as $label => $key)
        <div class="bg-slate-50 rounded-xl p-4">
            <p class="text-xs text-slate-400 mb-2">{{ $label }}</p>
            <p class="text-xl font-bold text-slate-700">
                @if(in_array($key, ['avg_cpi','avg_spi']))
                    {{ number_format($evaGlobal[$key] ?? 0, 2) }}
                @else
                    Rp {{ number_format(($evaGlobal[$key] ?? 0)/1000000, 1) }}Jt
                @endif
            </p>
            @php
                $val = $evaGlobal[$key] ?? 0;
                $isIndex = in_array($key, ['avg_cpi','avg_spi']);
                $good = $isIndex ? $val >= 1 : $val >= 0;
            @endphp
            <span class="text-xs {{ $good ? 'text-emerald-600' : 'text-red-500' }} font-medium">
                {{ $good ? '✓ On Track' : '⚠ Perlu Perhatian' }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endsection
