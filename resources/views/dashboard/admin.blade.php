@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Statistik & Pengelolaan Kursus')

@section('content')
    <div class="space-y-8">
        <!-- Stat Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Kursus</span>
                <div class="text-2xl font-black text-slate-800">{{ $stats['total_courses'] }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">{{ $stats['published_courses'] }}
                    Dipublikasikan</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Peserta</span>
                <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_students']) }}</div>
                <div class="text-[11px] text-slate-400 font-medium mt-1">Pengguna Terdaftar</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Enrollment</span>
                <div class="text-2xl font-black text-emerald-700">{{ number_format($stats['total_enrollments']) }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1">{{ $stats['total_completions'] }} Telah Lulus
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Tingkat Lulus Kuis</span>
                <div class="text-2xl font-black text-amber-600">{{ $stats['quiz_pass_rate'] }}%</div>
                <div class="text-[11px] text-slate-400 font-medium mt-1">{{ $stats['total_quizzes'] }} Kuis Aktif</div>
            </div>
        </div>

        <!-- Quick Action Banner -->
        <div
            class="bg-gradient-to-r from-emerald-800 to-slate-900 text-white p-6 rounded-3xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div>
                <h3 class="text-lg font-bold">Mulai Buat Kursus Baru</h3>
                <p class="text-xs text-emerald-200 mt-0.5">Susun kurikulum secara bertahap: Course &rarr; Module &rarr;
                    Lesson &rarr; Quiz</p>
            </div>
            <a href="{{ route('admin.courses.create') }}"
                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-md transition-colors shrink-0">
                + Tambah Kursus Baru
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Enrollments -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Pendaftaran Peserta Terbaru</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($recentEnrollments as $enr)
                        <div class="py-3 flex items-center justify-between">
                            <div class="truncate mr-3">
                                <span class="font-bold text-slate-800 block truncate">{{ $enr->user?->name ?? 'Peserta' }}</span>
                                <span class="text-slate-400 text-[11px] truncate block">{{ $enr->course?->title ?? 'Kursus Tidak Ditemukan' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span
                                    class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $enr->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
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
                                <span class="font-bold text-slate-800 block truncate">{{ $att->user?->name ?? 'Peserta' }}</span>
                                <span class="text-slate-400 text-[11px] truncate block">{{ $att->quiz?->title ?? 'Kuis Tidak Ditemukan' }}</span>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-slate-800">{{ $att->percentage }}%</span>
                                <span
                                    class="ml-1 px-2 py-0.5 rounded-full font-bold text-[10px] {{ $att->passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
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