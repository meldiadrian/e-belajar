@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Statistik & Pengelolaan Pembelajaran')

@section('content')
    <div class="space-y-8">
        @if(!Auth::user()->hasTwoFactorEnabled())
            <div class="p-4 sm:p-5 bg-gradient-to-r from-amber-50 to-orange-50/50 border border-amber-200/90 rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-bold text-slate-900">Perhatian Keamanan: Autentikasi 2FA Belum Aktif</h4>
                        <p class="text-xs text-slate-600 mt-0.5">Sebagai Admin Kursus, Anda sangat disarankan untuk mengaktifkan Google Authenticator guna mengamankan akses pengelolaan materi & nilai.</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit', Auth::id()) }}#two-factor-section"
                    class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0 flex items-center gap-1.5">
                    <span>Aktifkan 2FA</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endif

        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Pembelajaran</span>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_courses'] }}</div>
                <div class="text-[11px] text-[#4D52B4] font-semibold mt-1">{{ $stats['published_courses'] }}
                    Dipublikasikan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Peserta</span>
                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_students']) }}</div>
                <div class="text-[11px] text-slate-400 font-medium mt-1">Pengguna Terdaftar</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Peserta yang
                    Terdaftar</span>
                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_enrollments']) }}</div>
                <div class="text-[11px] text-[#4D52B4] font-semibold mt-1">{{ $stats['total_completions'] }} Telah Lulus
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Sertifikat Terbit</span>
                <div class="text-2xl font-black text-amber-600">{{ number_format($stats['total_certificates']) }}</div>
                <div class="text-[11px] text-slate-400 mt-1">{{ number_format($stats['total_quiz_attempts']) }} Percobaan
                    Kuis</div>
            </div>
        </div>

        <!-- Quick Action Banner -->
        <div
            class="bg-gradient-to-r from-[#4D52B4] to-slate-900 text-white p-6 rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold">Mulai Tambah Pembelajaran Baru</h3>
                <p class="text-xs text-[#4E9CE8] mt-0.5">Susun Pembelajaran secara bertahap</p>
            </div>
            <a href="{{ route('admin.courses.create') }}"
                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-md transition-colors shrink-0">
                + Tambah Pembelajaran Baru
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Enrollments -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Pendaftaran Peserta Terbaru
                </h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($recentEnrollments as $enr)
                        <div class="py-3 flex items-center justify-between">
                            <div class="truncate mr-3">
                                <span
                                    class="font-bold text-slate-800 block truncate">{{ $enr->user?->name ?? 'Peserta' }}</span>
                                <span
                                    class="text-slate-400 text-[11px] truncate block">{{ $enr->course?->title ?? 'Kursus Tidak Ditemukan' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $enr->status === 'completed' ? 'bg-[#4E9CE8]/20 text-[#4D52B4]' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($enr->status) }}
                                </span>
                                <span
                                    class="text-[10px] text-slate-400 block mt-0.5">{{ $enr->enrolled_at?->diffForHumans() ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-slate-400">Belum ada enrollment terbaru.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Quiz Submissions -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Hasil Pengerjaan Kuis Terbaru
                </h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($recentAttempts as $att)
                        <div class="py-3 flex items-center justify-between">
                            <div class="truncate mr-3">
                                <span
                                    class="font-bold text-slate-800 block truncate">{{ $att->user?->name ?? 'Peserta' }}</span>
                                <span
                                    class="text-slate-400 text-[11px] truncate block">{{ $att->quiz?->title ?? 'Kuis Tidak Ditemukan' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-slate-800">{{ $att->percentage }}%</span>
                                <span
                                    class="ml-1 px-2 py-0.5 rounded-full font-bold text-[10px] {{ $att->passed ? 'bg-[#4E9CE8]/20 text-[#4D52B4]' : 'bg-red-100 text-red-800' }}">
                                    {{ $att->passed ? 'Lulus' : 'Remidi' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-slate-400">Belum ada hasil kuis terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection