@extends('layouts.admin')

@section('title', 'Superadmin Dashboard')
@section('page_title', 'Pusat Kontrol Seluruh Sistem')

@section('content')
    <div class="space-y-8">
        @if(!Auth::user()->hasTwoFactorEnabled())
            <div class="p-4 sm:p-5 bg-gradient-to-r from-red-50 to-amber-50/60 border border-red-200/80 rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-red-500/10 text-red-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">Perhatian Kritis: Akun Superadmin Belum Memiliki 2FA</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Sebagai pemegang hak akses tertinggi sistem, wajibkan keamanan berlapis Google Authenticator untuk mencegah pengambilalihan akun.</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit', Auth::id()) }}#two-factor-section"
                    class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0 flex items-center gap-1.5">
                    <span>Aktifkan 2FA Sekarang</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif

        <!-- Top System Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Pengguna</span>
                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_users']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">{{ $stats['total_admins'] }} Admin &bull;
                    {{ $stats['total_superadmins'] }} Superadmin
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Pembelajaran
                    Terdaftar</span>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_courses'] }}</div>
                <div class="text-[11px] text-[#4D52B4] font-semibold mt-1">Dalam Database Sistem</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Peserta yang
                    Terdaftar</span>
                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_enrollments']) }}</div>
                <div class="text-[11px] text-blue-600 font-semibold mt-1">
                    {{ number_format($stats['total_lesson_completions']) }} Materi Diselesaikan
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Sertifikat Terbit</span>
                <div class="text-2xl font-black text-amber-600">{{ number_format($stats['total_certificates']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">{{ number_format($stats['total_quiz_attempts']) }} Percobaan
                    Kuis</div>
            </div>
        </div>

        <!-- Quick Shortcuts -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('superadmin.users.index') }}"
                class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-[#4E9CE8] shadow-xs transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-[#4E9CE8]/20 text-[#4D52B4] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-[#4E9CE8]">Kelola Pengguna & Peran
                            (RBAC)</h4>
                        <p class="text-xs text-slate-500">Atur hak akses Superadmin, Admin, dan Peserta</p>
                    </div>
                </div>
                <span class="text-slate-400 group-hover:text-[#4E9CE8] font-bold">&rarr;</span>
            </a>

            <a href="{{ route('superadmin.activity-logs.index') }}"
                class="p-5 rounded-2xl bg-white border border-slate-200 hover:border-[#4E9CE8] shadow-xs transition-all flex items-center justify-between group">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm group-hover:text-amber-700">Audit Jejak Aktivitas Sistem
                        </h4>
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
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Aktivitas Sistem Terkini (Audit
                        Trail)</h3>
                    <span class="text-xs text-slate-500">Mencatat user, event, entitas, IP, dan timestamp</span>
                </div>
                <a href="{{ route('superadmin.activity-logs.index') }}"
                    class="text-xs font-semibold text-[#4D52B4] hover:underline">Semua Log &rarr;</a>
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
                                <td class="py-3 px-4 text-slate-400 font-mono whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $log->user->name ?? 'Sistem' }}</td>
                                <td class="py-3 px-4">
                                    <span
                                        class="px-2 py-0.5 rounded-full font-mono text-[10px] font-bold bg-slate-100 text-slate-700">
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