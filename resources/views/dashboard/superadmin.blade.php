@extends('layouts.admin')

@section('title', 'Superadmin Dashboard')
@section('page_title', 'Pusat Kontrol Seluruh Sistem')

@section('content')
<div class="space-y-8">
    <!-- Top System Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Pengguna</span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_users']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ $stats['total_admins'] }} Admin &bull; {{ $stats['total_superadmins'] }} Superadmin</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Kursus Terdaftar</span>
            <div class="text-2xl font-black text-slate-900">{{ $stats['total_courses'] }}</div>
            <div class="text-[11px] text-emerald-600 font-semibold mt-1">Dalam Database Sistem</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Enrollment</span>
            <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_enrollments']) }}</div>
            <div class="text-[11px] text-blue-600 font-semibold mt-1">{{ number_format($stats['total_lesson_completions']) }} Lesson Diselesaikan</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Sertifikat Terbit</span>
            <div class="text-2xl font-black text-amber-600">{{ number_format($stats['total_certificates']) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">{{ number_format($stats['total_quiz_attempts']) }} Percobaan Kuis</div>
        </div>
    </div>

    <!-- Quick Shortcuts -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('superadmin.users.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 shadow-xs transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-emerald-700">Kelola Pengguna & Peran (RBAC)</h4>
                    <p class="text-xs text-slate-500">Atur hak akses Superadmin, Admin, dan Peserta</p>
                </div>
            </div>
            <span class="text-slate-400 group-hover:text-emerald-700 font-bold">&rarr;</span>
        </a>

        <a href="{{ route('superadmin.activity-logs.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-emerald-500 shadow-xs transition-all flex items-center justify-between group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700">Audit Jejak Aktivitas Sistem</h4>
                    <p class="text-xs text-slate-500">Lihat riwayat perubahan data, login, dan IP</p>
                </div>
            </div>
            <span class="text-slate-400 group-hover:text-amber-700 font-bold">&rarr;</span>
        </a>
    </div>

    <!-- System Audit Activity Logs Stream -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Sistem Terkini (Audit Trail)</h3>
                <span class="text-xs text-slate-500">Mencatat user, event, entitas, IP, dan timestamp</span>
            </div>
            <a href="{{ route('superadmin.activity-logs.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Semua Log &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-y border-slate-100 font-bold">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Aksi / Event</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 text-slate-400 font-mono whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-bold text-slate-800">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full font-mono text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs truncate">{{ $log->description }}</td>
                            <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada catatan aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
