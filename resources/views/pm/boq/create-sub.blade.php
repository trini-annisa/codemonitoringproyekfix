@extends('layouts.app')
@section('title', 'Tambah Item Pekerjaan')
@section('page-title', 'Tambah Item Pekerjaan BOQ')
@section('page-subtitle', '{{ $project->name }} — {{ $item->code }} {{ $item->name }}')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">

        <div class="mb-5 p-4 bg-emerald-50 rounded-xl flex gap-6">
            <div>
                <p class="text-xs text-emerald-600 font-medium">Proyek</p>
                <p class="text-sm font-bold text-emerald-800">{{ $project->name }}</p>
            </div>
            <div>
                <p class="text-xs text-emerald-600 font-medium">Kelompok</p>
                <p class="text-sm font-bold text-emerald-800">{{ $item->code }} — {{ $item->name }}</p>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('pm.boq.sub.store', $item) }}">
            @csrf

            <div class="space-y-4">

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="Contoh: A.1"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('code') border-red-400 @enderror">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Uraian Pekerjaan <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Contoh: Pembersihan Lahan"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('name') border-red-400 @enderror">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Spesifikasi</label>
                    <textarea name="specification" rows="2"
                              placeholder="Spesifikasi teknis pekerjaan (opsional)"
                              class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('specification') }}</textarea>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Volume <span class="text-red-500">*</span></label>
                        <input type="number" name="volume" value="{{ old('volume') }}"
                               step="0.001" min="0.001"
                               placeholder="0.000"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('volume') border-red-400 @enderror"
                               id="volume" oninput="calcTotal()">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit') }}"
                               placeholder="m², m³, ls, unit"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('unit') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Urutan <span class="text-red-500">*</span></label>
                        <input type="number" name="order_no" value="{{ old('order_no', 1) }}" min="1"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Harga Satuan (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="unit_price" value="{{ old('unit_price') }}"
                               min="0" placeholder="0"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('unit_price') border-red-400 @enderror"
                               id="unit_price" oninput="calcTotal()">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jumlah Harga (otomatis)</label>
                        <div class="w-full border border-slate-100 bg-slate-50 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700" id="total_display">
                            Rp 0
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Mulai Rencana <span class="text-red-500">*</span></label>
                        <input type="date" name="planned_start" value="{{ old('planned_start', $project->start_date?->format('Y-m-d')) }}"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('planned_start') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Selesai Rencana <span class="text-red-500">*</span></label>
                        <input type="date" name="planned_end" value="{{ old('planned_end', $project->end_date?->format('Y-m-d')) }}"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none @error('planned_end') border-red-400 @enderror">
                    </div>
                </div>

            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('pm.boq.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    ← Batal
                </a>
                <button type="submit"
                        class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700">
                    Simpan Item →
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function calcTotal() {
    const vol   = parseFloat(document.getElementById('volume').value) || 0;
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = vol * price;
    document.getElementById('total_display').textContent =
        'Rp ' + total.toLocaleString('id-ID', {minimumFractionDigits: 0});
}
</script>
@endpush

@endsection
