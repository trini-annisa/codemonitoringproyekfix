@extends('layouts.app')
@section('title', 'Proyek Saya')
@section('page-title', 'Proyek Saya')
@section('page-subtitle', 'Daftar proyek yang Anda kelola')

@section('content')

@if(session('success'))
<div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium">
    ✓ {{ session('success') }}
</div>
@endif

<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-slate-500">Total: <span class="font-semibold text-slate-700">{{ $projects->count() }} proyek</span></p>
    <a href="{{ route('pm.projects.create') }}"
       class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors">
        + Tambah Proyek
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50">
                <th class="text-left px-6 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Kode</th>
                <th class="text-left px-6 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Nama Pekerjaan</th>
                <th class="text-left px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Lokasi</th>
                <th class="text-right px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">BAC</th>
                <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Status</th>
                <th class="text-center px-4 py-3 text-xs text-slate-400 font-semibold uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($projects as $project)
            <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $project->project_code }}</td>
                <td class="px-6 py-4">
                    <p class="font-semibold text-slate-700">{{ $project->name }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $project->owner }}</p>
                </td>
                <td class="px-4 py-4 text-slate-600">{{ $project->location ?? '-' }}</td>
                <td class="px-4 py-4 text-right font-semibold text-slate-700">
                    Rp {{ number_format($project->bac / 1000000, 1) }}Jt
                </td>
                <td class="px-4 py-4 text-center">
                    @php
                        $statusConfig = [
                            'active'    => ['bg-emerald-50 text-emerald-700', 'Aktif'],
                            'completed' => ['bg-blue-50 text-blue-700', 'Selesai'],
                            'on_hold'   => ['bg-amber-50 text-amber-700', 'On Hold'],
                        ];
                        $cfg = $statusConfig[$project->status] ?? ['bg-slate-50 text-slate-500', ucfirst($project->status)];
                    @endphp
                    <span class="status-badge {{ $cfg[0] }}">{{ $cfg[1] }}</span>
                </td>
                <td class="px-4 py-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                       <a href="{{ route('pm.eva.show', $project) }}"
   class="text-xs bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition font-medium">
    Laporan EVA
</a>
                        <a href="{{ route('pm.projects.edit', $project) }}"
                           class="text-xs bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg hover:bg-amber-100 transition font-medium">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('pm.projects.destroy', $project) }}"
                              onsubmit="return confirm('Yakin hapus proyek {{ addslashes($project->name) }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-100 transition font-medium">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                    <p class="mb-3">Belum ada proyek</p>
                    <a href="{{ route('pm.projects.create') }}"
                       class="text-blue-600 hover:underline text-sm font-medium">
                        + Tambah proyek pertama Anda
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
