@extends('layouts.app')
@section('title', 'Input Progress')
@section('page-title', 'Input Progress Pekerjaan')
@section('page-subtitle', 'Catat realisasi fisik dan biaya aktual per periode pelaporan')

@section('content')
<div class="max-w-5xl mx-auto">

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
    </div>
    @endif

    {{-- STEP 1: PILIH PROYEK & PERIODE --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-5">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <span class="text-blue-600 font-bold text-sm">1</span>
            </div>
            <h3 class="font-semibold text-slate-700">Pilih Proyek & Periode</h3>
        </div>

        <form method="GET" action="{{ route('pm.progress.create') }}" class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Proyek</label>
                <select name="project_id" onchange="this.form.submit()"
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Pilih Proyek --</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ $selectedProject?->id == $p->id ? 'selected' : '' }}>
                        {{ $p->project_code }} — {{ $p->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if($selectedProject && $subItems->isNotEmpty())

    {{-- STEP 2: INPUT PROGRESS --}}
    <form method="POST" action="{{ route('pm.progress.store') }}">
        @csrf
        <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">

        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <span class="text-emerald-600 font-bold text-sm">2</span>
                </div>
                <h3 class="font-semibold text-slate-700">Periode Pelaporan</h3>
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Label Periode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="period_label"
                           value="{{ old('period_label') }}"
                           placeholder="Contoh: M-01, B-01, W-01"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <p class="text-xs text-slate-400 mt-1">M=Minggu, B=Bulan, W=Week</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Tanggal Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="report_date"
                           value="{{ old('report_date', now()->format('Y-m-d')) }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>
        </div>

        {{-- STEP 3: INPUT PER SUB-ITEM --}}
        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-5">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <span class="text-violet-600 font-bold text-sm">3</span>
                </div>
                <h3 class="font-semibold text-slate-700">Input Realisasi per Item Pekerjaan</h3>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Kode</th>
                        <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Uraian Pekerjaan</th>
                        <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Nilai Kontrak</th>
                        <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Progress Fisik (%)</th>
                        <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Biaya Aktual (Rp)</th>
                        <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($subItems as $i => $sub)
                    <input type="hidden" name="entries[{{ $i }}][boq_sub_item_id]" value="{{ $sub->id }}">
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $sub->code }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-700">{{ $sub->name }}</p>
                            <p class="text-xs text-slate-400">{{ $sub->boqItem->name }}</p>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-600 text-xs">
                            Rp {{ number_format($sub->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <input type="number"
                                       name="entries[{{ $i }}][physical_progress]"
                                       value="{{ old("entries.{$i}.physical_progress", 0) }}"
                                       min="0" max="100" step="0.01"
                                       class="w-20 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-center focus:ring-2 focus:ring-blue-500 outline-none">
                                <span class="text-slate-400 text-xs">%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number"
                                   name="entries[{{ $i }}][actual_cost]"
                                   value="{{ old("entries.{$i}.actual_cost", 0) }}"
                                   min="0" step="1"
                                   class="w-36 border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-right focus:ring-2 focus:ring-blue-500 outline-none">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text"
                                   name="entries[{{ $i }}][notes]"
                                   value="{{ old("entries.{$i}.notes") }}"
                                   placeholder="Opsional"
                                   class="w-full border border-slate-200 rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('pm.progress.index') }}"
               class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                ← Batal
            </a>
            <button type="submit"
                    class="px-8 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                Simpan & Hitung EVA Otomatis →
            </button>
        </div>
    </form>

    @elseif($selectedProject && $subItems->isEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-700">
        ⚠ Proyek ini belum memiliki data BOQ.
        <a href="{{ route('pm.boq.index') }}" class="underline font-medium">Input BOQ dulu →</a>
    </div>
    @endif

</div>
@endsection
