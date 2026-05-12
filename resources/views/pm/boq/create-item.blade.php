@extends('layouts.app')
@section('title', 'Tambah Kelompok Pekerjaan')
@section('page-title', 'Tambah Kelompok Pekerjaan BOQ')
@section('page-subtitle', '{{ $project->name }}')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">

        <div class="mb-5 p-4 bg-blue-50 rounded-xl">
            <p class="text-sm font-semibold text-blue-700">{{ $project->project_code }} — {{ $project->name }}</p>
            <p class="text-xs text-blue-500 mt-0.5">BAC: Rp {{ number_format($project->bac/1000000,1) }}Jt</p>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('pm.boq.items.store', $project) }}">
            @csrf

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                            Kode <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="Contoh: A, B, I, II"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition uppercase @error('code') border-red-400 @enderror">
                        <p class="text-xs text-slate-400 mt-1">Kode divisi pekerjaan</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                            Urutan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="order_no" value="{{ old('order_no', 1) }}"
                               min="1"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition @error('order_no') border-red-400 @enderror">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">
                        Nama Kelompok Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Contoh: Pekerjaan Persiapan"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition @error('name') border-red-400 @enderror">
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('pm.boq.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    ← Batal
                </a>
                <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                    Simpan →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
