@extends('layouts.app')
@section('title', 'Kelola BOQ')
@section('page-title', 'Kelola BOQ')
@section('page-subtitle', 'Bill of Quantity — Rencana Anggaran Biaya Proyek')

@section('content')

@if(session('success'))
<div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium">
    ✓ {{ session('success') }}
</div>
@endif

@forelse($projects as $project)
<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-6">

    {{-- PROJECT HEADER --}}
    <div class="gradient-header px-6 py-4 flex items-center justify-between">
        <div>
            <p class="text-white font-bold">{{ $project->name }}</p>
            <p class="text-blue-300 text-xs font-mono mt-0.5">
                {{ $project->project_code }} · BAC: Rp {{ number_format($project->bac/1000000, 1) }}Jt
            </p>
        </div>
        <a href="{{ route('pm.boq.items.create', $project) }}"
           class="bg-blue-500 hover:bg-blue-400 text-white text-xs px-4 py-2 rounded-lg font-semibold transition">
            + Kelompok Pekerjaan
        </a>
    </div>

    @if($project->boqItems->isEmpty())
    <div class="px-6 py-10 text-center text-slate-400 text-sm">
        Belum ada data BOQ untuk proyek ini.
        <a href="{{ route('pm.boq.items.create', $project) }}" class="text-blue-600 hover:underline ml-1">
            Tambah sekarang →
        </a>
    </div>
    @else

    @php $grandTotal = $project->boqItems->sum('subtotal'); @endphp

    @foreach($project->boqItems as $item)
    <div class="border-b border-slate-50 last:border-0">

        {{-- ITEM HEADER --}}
        <div class="px-6 py-3 bg-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono bg-blue-100 text-blue-700 px-2 py-0.5 rounded font-bold">
                    {{ $item->code }}
                </span>
                <span class="font-semibold text-slate-700 text-sm">{{ $item->name }}</span>
                <span class="text-xs text-slate-400">
                    Subtotal: <span class="font-semibold text-slate-600">Rp {{ number_format($item->subtotal/1000000, 2) }}Jt</span>
                    · Bobot: <span class="font-semibold text-slate-600">{{ number_format($item->weight_percent, 2) }}%</span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pm.boq.sub.create', $item) }}"
                   class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition font-medium">
                    + Sub Item
                </a>
                <form method="POST" action="{{ route('pm.boq.items.destroy', $item) }}"
                      onsubmit="return confirm('Hapus kelompok {{ addslashes($item->name) }} beserta semua sub-itemnya?')">
                    @csrf @method('DELETE')
                    <button class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition font-medium">
                        Hapus
                    </button>
                </form>
            </div>
        </div>

        {{-- SUB ITEMS TABLE --}}
        @if($item->subItems->isNotEmpty())
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-white border-b border-slate-50">
                    <th class="text-left px-6 py-2 text-slate-400 font-semibold">Kode</th>
                    <th class="text-left px-4 py-2 text-slate-400 font-semibold">Uraian Pekerjaan</th>
                    <th class="text-right px-4 py-2 text-slate-400 font-semibold">Volume</th>
                    <th class="text-left px-2 py-2 text-slate-400 font-semibold">Sat.</th>
                    <th class="text-right px-4 py-2 text-slate-400 font-semibold">Harga Satuan</th>
                    <th class="text-right px-4 py-2 text-slate-400 font-semibold">Jumlah Harga</th>
                    <th class="text-right px-4 py-2 text-slate-400 font-semibold">Bobot</th>
                    <th class="text-center px-4 py-2 text-slate-400 font-semibold">Jadwal</th>
                    <th class="text-center px-4 py-2 text-slate-400 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($item->subItems as $sub)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-2.5 font-mono text-slate-500">{{ $sub->code }}</td>
                    <td class="px-4 py-2.5 text-slate-700 font-medium">{{ $sub->name }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($sub->volume, 3) }}</td>
                    <td class="px-2 py-2.5 text-slate-500">{{ $sub->unit }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($sub->unit_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-slate-700">{{ number_format($sub->total_price, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($sub->weight_percent, 2) }}%</td>
                    <td class="px-4 py-2.5 text-center text-slate-500">
                        {{ $sub->planned_start->format('d/m/y') }} – {{ $sub->planned_end->format('d/m/y') }}
                    </td>
                    <td class="px-4 py-2.5 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('pm.boq.sub.edit', $sub) }}"
                               class="text-amber-600 hover:text-amber-800 font-medium">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('pm.boq.sub.destroy', $sub) }}"
                                  onsubmit="return confirm('Hapus item ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 font-medium">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 border-t border-slate-100">
                    <td colspan="5" class="px-6 py-2.5 text-xs font-bold text-slate-600 text-right">
                        Subtotal {{ $item->code }}:
                    </td>
                    <td class="px-4 py-2.5 text-right font-bold text-slate-700">
                        {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-right font-bold text-slate-700">
                        {{ number_format($item->weight_percent, 2) }}%
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        @endif

    </div>
    @endforeach

    {{-- GRAND TOTAL --}}
    <div class="px-6 py-4 bg-slate-800 flex justify-between items-center">
        <span class="text-white font-bold text-sm">TOTAL BOQ</span>
        <span class="text-white font-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
    </div>

    @endif
</div>
@empty
<div class="bg-white rounded-2xl border border-slate-100 p-16 text-center">
    <p class="text-slate-400 mb-3">Belum ada proyek yang ditugaskan.</p>
    <a href="{{ route('pm.projects.create') }}"
       class="bg-blue-600 text-white px-6 py-2 rounded-xl text-sm font-medium hover:bg-blue-700">
        + Buat Proyek Dulu
    </a>
</div>
@endforelse

@endsection
