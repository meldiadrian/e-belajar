@extends('layouts.admin')

@section('title', 'Audit Log Aktivitas Sistem')
@section('page_title', 'Audit Trail Aktivitas')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-black text-slate-900">Jejak Audit Aktivitas Sistem</h2>
        <p class="text-xs text-slate-500">Mencatat riwayat login, manipulasi kursus, enrollment, kuis, dan penerbitan sertifikat</p>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('superadmin.activity-logs.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-wrap gap-4 items-center justify-between">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, atau IP..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:outline-hidden">
        </div>
        <div>
            <select name="action" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white" onchange="this.form.submit()">
                <option value="">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800">
            Cari
        </button>
    </form>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-100 font-bold">
                    <tr>
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">Aksi / Event</th>
                        <th class="py-3.5 px-4">Deskripsi Aktivitas</th>
                        <th class="py-3.5 px-4">IP & User Agent</th>
                        <th class="py-3.5 px-4 text-right">Data Perubahan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                {{ $log->user->name ?? 'Sistem / Tamu' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full font-mono text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 leading-snug">
                                {{ $log->description }}
                            </td>
                            <td class="py-3.5 px-4 text-[11px] text-slate-400">
                                <div class="font-mono text-slate-600">{{ $log->ip_address ?? '-' }}</div>
                                <div class="truncate max-w-[150px] text-[10px]">{{ $log->user_agent }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                @if($log->old_values || $log->new_values)
                                    <button onclick="showDiffModal({{ json_encode($log->old_values) }}, {{ json_encode($log->new_values) }})" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-[10px]">
                                        Lihat JSON
                                    </button>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada log aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $logs->links() }}
    </div>
</div>

<!-- Modal: JSON Diff Viewer -->
<div id="modalJson" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl p-6 max-w-2xl w-full shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center">
            <h3 class="text-base font-bold text-slate-900">Rincian Perubahan Data (Audit)</h3>
            <button onclick="document.getElementById('modalJson').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
            <div>
                <span class="font-bold text-slate-700 block mb-1">Nilai Lama (Old Values):</span>
                <pre id="jsonOld" class="p-3 rounded-xl bg-slate-100 text-slate-800 overflow-x-auto h-64 whitespace-pre-wrap"></pre>
            </div>
            <div>
                <span class="font-bold text-emerald-700 block mb-1">Nilai Baru (New Values):</span>
                <pre id="jsonNew" class="p-3 rounded-xl bg-emerald-50 text-emerald-900 overflow-x-auto h-64 whitespace-pre-wrap"></pre>
            </div>
        </div>
    </div>
</div>

<script>
    function showDiffModal(oldVal, newVal) {
        document.getElementById('jsonOld').innerText = JSON.stringify(oldVal, null, 2) || 'null';
        document.getElementById('jsonNew').innerText = JSON.stringify(newVal, null, 2) || 'null';
        document.getElementById('modalJson').classList.remove('hidden');
    }
</script>
@endsection
