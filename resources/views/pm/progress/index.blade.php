@extends('layouts.app')
@section('title', 'Input Progress')
@section('page-title', 'Input Progress Pekerjaan')
@section('page-subtitle', 'Catat realisasi fisik dan biaya aktual per periode')

@section('content')

@if(session('success'))
<div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium">
    ✓ {{ session('success') }}
</div>
@endif

<div class="flex justify-end mb-6">
    <a href="{{ route('pm.progress.create') }}"
       class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
        + Input Progress Baru
    </a>
</div>

@forelse($projects as $project)
@php $periodGroups = $progressByProject[$project->id] ?? collect(); @endphp

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-5">

    {{-- PROJECT HEADER --}}
    <div class="gradient-header px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-white font-bold">{{ $project->name }}</p>
            <p class="text-blue-300 text-xs font-mono">{{ $project->project_code }}</p>
        </div>
        <a href="{{ route('pm.progress.create') }}?project_id={{ $project->id }}"
           class="bg-blue-500 hover:bg-blue-400 text-white text-xs px-4 py-2 rounded-lg font-semibold">
            + Input Progress
        </a>
    </div>

    @if($periodGroups->isEmpty())
    <div class="px-6 py-8 text-center text-slate-400 text-sm">
        Belum ada data progress untuk proyek ini.
    </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50">
                <th class="text-left px-6 py-3 text-xs text-slate-400 font-semibold uppercase">Periode</th>
                <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Tanggal</th>
                <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Item</th>
                <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Avg Progress</th>
                <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Biaya Aktual</th>
                <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Earned Value</th>
                <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($periodGroups as $group)
            <tr class="hover:bg-slate-50/50" x-data="{ open: false }">
                <td class="px-6 py-3 font-mono font-semibold text-slate-700">
                    {{ $group['period_label'] }}
                </td>
                <td class="px-4 py-3 text-slate-600">
                    {{ $group['report_date']->format('d/m/Y') }}
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-0.5 rounded-full font-medium">
                        {{ $group['entry_count'] }} item
                    </span>
                </td>
                <td class="px-4 py-3 text-right font-semibold text-blue-600">
                    {{ number_format($group['avg_progress'], 1) }}%
                </td>
                <td class="px-4 py-3 text-right text-slate-600">
                    Rp {{ number_format($group['total_ac'] / 1000000, 2) }}Jt
                </td>
                <td class="px-4 py-3 text-right text-emerald-600 font-semibold">
                    Rp {{ number_format($group['total_ev'] / 1000000, 2) }}Jt
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Tombol edit mengarah ke entry pertama periode ini --}}
                        <a href="{{ route('pm.progress.create') }}?project_id={{ $project->id }}&period={{ $group['period_label'] }}"
                           class="text-xs bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-100 font-medium">
                            Edit Periode
                        </a>
                        {{-- Hapus semua entry periode ini --}}
                        <form method="POST"
                              action="{{ route('pm.progress.destroyPeriod') }}"
                              onsubmit="return confirm('Hapus semua data periode {{ $group['period_label'] }}?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                            <input type="hidden" name="period_label" value="{{ $group['period_label'] }}">
                            <button class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 font-medium">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@empty
<div class="bg-white rounded-2xl border border-slate-100 p-16 text-center text-slate-400">
    Belum ada proyek.
</div>
@endforelse

@endsection
