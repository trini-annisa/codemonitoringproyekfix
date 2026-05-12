@extends('layouts.app')
@section('title', 'Edit Proyek')
@section('page-title', 'Edit Proyek')
@section('page-subtitle', 'Perbarui data proyek konstruksi')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- HEADER CARD --}}
    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden mb-6">
        <div class="gradient-header px-8 py-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-lg">Edit Data Proyek</h2>
                    <p class="text-blue-300 text-sm font-mono">{{ $project->project_code }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm">
        <p class="font-semibold mb-1">Terdapat kesalahan input:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('pm.projects.update', $project) }}">
        @csrf
        @method('PUT')

        {{-- SECTION 1: IDENTITAS PROYEK --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-sm">1</span>
                </div>
                <h3 class="font-semibold text-slate-700">Identitas Proyek</h3>
            </div>

            <div class="grid grid-cols-2 gap-5">

                {{-- Nama Kegiatan --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kegiatan</label>
                    <input type="text" name="description"
                           value="{{ old('description', $project->description) }}"
                           placeholder="Contoh: Pembangunan Gedung Perkantoran Tahap II"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    <p class="text-xs text-slate-400 mt-1">Program/kegiatan induk dari pekerjaan ini</p>
                </div>

                {{-- Nama Pekerjaan --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Nama Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $project->name) }}"
                           placeholder="Contoh: Pekerjaan Struktur Beton Bertulang Lt. 1-5"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- No Kontrak --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nomor Kontrak</label>
                    <input type="text" name="contract_number"
                           value="{{ old('contract_number', $project->contract_number) }}"
                           placeholder="Contoh: 027/SPK/DPU/2024"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Lokasi Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="location"
                           value="{{ old('location', $project->location) }}"
                           placeholder="Contoh: Jl. Sudirman No. 1, Makassar"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('location') border-red-400 @enderror">
                    @error('location')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- SECTION 2: PIHAK-PIHAK --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center">
                    <span class="text-violet-600 font-bold text-sm">2</span>
                </div>
                <h3 class="font-semibold text-slate-700">Pihak-Pihak Terkait</h3>
            </div>

            <div class="grid grid-cols-2 gap-5">

                {{-- Owner --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Pemilik Proyek (Owner) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="owner"
                           value="{{ old('owner', $project->owner) }}"
                           placeholder="Contoh: Dinas Pekerjaan Umum Kota Makassar"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('owner') border-red-400 @enderror">
                    @error('owner')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Status Proyek <span class="text-red-500">*</span>
                    </label>
                    <select name="status"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('status') border-red-400 @enderror">
                        <option value="active"    {{ old('status', $project->status) === 'active'    ? 'selected' : '' }}>🟢 Aktif</option>
                        <option value="on_hold"   {{ old('status', $project->status) === 'on_hold'   ? 'selected' : '' }}>🟡 On Hold</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>🔵 Selesai</option>
                    </select>
                    @error('status')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- SECTION 3: NILAI & WAKTU --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-6 mb-5">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <span class="text-emerald-600 font-bold text-sm">3</span>
                </div>
                <h3 class="font-semibold text-slate-700">Nilai Kontrak & Jadwal</h3>
            </div>

            <div class="grid grid-cols-2 gap-5">

                {{-- Nilai Kontrak --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Nilai Kontrak (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                        <input type="number" name="contract_value"
                               value="{{ old('contract_value', $project->contract_value) }}"
                               min="0"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('contract_value') border-red-400 @enderror">
                    </div>
                    @error('contract_value')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- BAC --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        BAC — Budget at Completion (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                        <input type="number" name="bac"
                               value="{{ old('bac', $project->bac) }}"
                               min="0"
                               class="w-full border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('bac') border-red-400 @enderror">
                    </div>
                    @error('bac')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date"
                           value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('start_date') border-red-400 @enderror">
                    @error('start_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="end_date"
                           value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition @error('end_date') border-red-400 @enderror">
                    @error('end_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('pm.projects.index') }}"
               class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                ← Batal
            </a>
            <button type="submit"
                    class="px-8 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 transition-colors shadow-sm">
                Simpan Perubahan →
            </button>
        </div>

    </form>
</div>

@endsection
