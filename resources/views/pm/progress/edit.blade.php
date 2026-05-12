@extends('layouts.app')
@section('title', 'Edit Progress')
@section('page-title', 'Edit Progress Pekerjaan')
@section('page-subtitle', '{{ $project->name }} — Periode {{ $entry->period_label }}')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">

        <div class="mb-5 p-4 bg-amber-50 rounded-xl">
            <p class="text-xs text-amber-600 font-medium">Item Pekerjaan</p>
            <p class="text-sm font-bold text-amber-800">{{ $subItem->code }} — {{ $subItem->name }}</p>
            <p class="text-xs text-amber-600 mt-1">
                Periode: <span class="font-semibold">{{ $entry->period_label }}</span> ·
                Tanggal: <span class="font-semibold">{{ $entry->report_date->format('d/m/Y') }}</span> ·
                Nilai: <span class="font-semibold">Rp {{ number_format($subItem->total_price, 0, ',', '.') }}</span>
            </p>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('pm.progress.update', $entry) }}">
            @csrf @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Progress Fisik (%) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="physical_progress"
                               value="{{ old('physical_progress', $entry->physical_progress) }}"
                               min="0" max="100" step="0.01"
                               class="w-32 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('physical_progress') border-red-400 @enderror">
                        <span class="text-slate-500 text-sm">%</span>
                        <span class="text-xs text-slate-400">
                            EV = {{ number_format($subItem->total_price/1000000, 1) }}Jt × progress%
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Biaya Aktual (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                        <input type="number" name="actual_cost"
                               value="{{ old('actual_cost', $entry->actual_cost) }}"
                               min="0"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('actual_cost') border-red-400 @enderror">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('notes', $entry->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('pm.progress.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    ← Batal
                </a>
                <button type="submit"
                        class="px-8 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600">
                    Simpan & Hitung Ulang EVA →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
