@extends('layouts.app')

@section('title', 'Dashboard Peserta - E-Belajar Kabupaten Bengkalis')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Welcome Header -->
        <div
            class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white rounded-3xl p-8 mb-10 shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Portal Pembelajaran
                            Peserta</span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold mt-1">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="text-xs sm:text-sm text-emerald-200 mt-2 max-w-xl">
                            Instansi: {{ Auth::user()->institution ?? 'Masyarakat Umum' }} &bull; Lanjutkan materi
                            pembelajaran mandiri Anda untuk menyelesaikan kurikulum dan mendapatkan sertifikat.
                        </p>
                    </div>
                    <a href="{{ route('profile.edit', Auth::id()) }}"
                        class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <span>Edit Profil Saya</span>
                    </a>
                </div>
            </div>
            <div class="absolute -bottom-10 -right-10 w-48 h-48 bg-emerald-500/20 rounded-full blur-2xl"></div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Kursus Diikuti</span>
                <div class="text-2xl font-black text-slate-800">{{ $stats['total_enrolled'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Sedang Berjalan</span>
                <div class="text-2xl font-black text-amber-600">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Kursus Selesai</span>
                <div class="text-2xl font-black text-emerald-600">{{ $stats['completed_courses'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Sertifikat Kelulusan</span>
                <div class="text-2xl font-black text-amber-600">{{ $stats['total_certificates'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Column: Enrolled Courses & Progress -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Active Courses -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Kursus Yang Sedang Diikuti</h2>
                            <span class="text-xs text-slate-500">Pantau progres dan lanjutkan belajar</span>
                        </div>
                        <a href="{{ route('courses.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">+
                            Cari Kursus Baru</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($enrollments as $enrollment)
                            <div
                                class="p-5 rounded-2xl border border-slate-100 bg-slate-50/60 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                                        {{ $enrollment->course->category->name ?? 'Program' }}
                                    </span>
                                    <h3 class="text-base font-bold text-slate-900 truncate mt-1">
                                        <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                            class="hover:text-emerald-700">
                                            {{ $enrollment->course->title }}
                                        </a>
                                    </h3>

                                    <div class="mt-3 space-y-1">
                                        <div class="flex justify-between text-xs text-slate-500 font-semibold">
                                            <span>Progres: {{ $enrollment->progress->progress_percentage ?? 0 }}%</span>
                                            <span>{{ $enrollment->progress->completed_lessons ?? 0 }} dari
                                                {{ $enrollment->progress->total_lessons ?? $enrollment->course->lessons_count }}
                                                Pelajaran</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-600 h-2 rounded-full transition-all duration-500"
                                                style="width: {{ $enrollment->progress->progress_percentage ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex sm:flex-col gap-2 w-full sm:w-auto">
                                    <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors text-center shadow-xs w-full sm:w-auto">
                                        Lanjutkan Belajar &rarr;
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400 text-xs">
                                Anda belum terdaftar pada kursus apapun. <a href="{{ route('courses.index') }}"
                                    class="font-bold text-emerald-700 underline">Lihat Katalog Kursus</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Learning History (Riwayat Pembelajaran) -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Riwayat Aktivitas Belajar</h2>
                    <div class="divide-y divide-slate-100 text-xs">
                        @forelse($learningHistory as $history)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-slate-800">{{ $history->lesson->title }}</span>
                                    <span
                                        class="text-slate-400 block mt-0.5">{{ $history->lesson->module->course->title ?? '' }}</span>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $history->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $history->status === 'completed' ? 'Selesai' : 'Sedang Dipelajari' }}
                                    </span>
                                    <span
                                        class="text-[10px] text-slate-400 block mt-0.5">{{ $history->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400">Belum ada riwayat aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Shortcuts, Quiz, Certificates, Notifications -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Last Lesson Shortcut -->
                @if($lastLessonProgress)
                    <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-2xl">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 block mb-1">Terakhir
                            Dipelajari</span>
                        <h4 class="text-sm font-bold text-emerald-950 truncate">{{ $lastLessonProgress->lesson->title }}</h4>
                        <p class="text-xs text-emerald-700 mt-1 truncate">
                            {{ $lastLessonProgress->lesson->module->course->title ?? '' }}</p>
                        <a href="{{ route('learning.lesson', [$lastLessonProgress->lesson->module->course_id, $lastLessonProgress->lesson_id]) }}"
                            class="mt-3 inline-block px-4 py-2 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800 transition-colors">
                            Buka Materi Kembali &rarr;
                        </a>
                    </div>
                @endif

                <!-- Last Quiz Attempt -->
                @if($lastQuizAttempt)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Evaluasi Kuis
                            Terakhir</span>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $lastQuizAttempt->quiz->title }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">Nilai: <span
                                        class="font-bold text-slate-800">{{ $lastQuizAttempt->score }}/{{ $lastQuizAttempt->max_score }}
                                        ({{ $lastQuizAttempt->percentage }}%)</span></div>
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-bold {{ $lastQuizAttempt->passed ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                {{ $lastQuizAttempt->passed ? 'LULUS' : 'REMIDI' }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Earned Certificates -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Sertifikat Saya</h3>
                        <a href="{{ route('my.certificates') }}"
                            class="text-xs text-emerald-700 font-semibold hover:underline">Semua</a>
                    </div>

                    <div class="space-y-2">
                        @forelse($certificates as $cert)
                            <div
                                class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                                <div class="truncate mr-2">
                                    <div class="font-bold text-slate-800 truncate">{{ $cert->course->title }}</div>
                                    <div class="text-[10px] font-mono text-emerald-700">{{ $cert->certificate_code }}</div>
                                </div>
                                <a href="{{ route('certificates.show', $cert->id) }}"
                                    class="px-2.5 py-1 bg-amber-400 hover:bg-amber-300 text-amber-950 font-bold rounded-lg text-[11px] shrink-0">
                                    Lihat
                                </a>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 py-3 text-center">Selesaikan kursus 100% untuk memperoleh
                                sertifikat resmi.</div>
                        @endforelse
                    </div>
                </div>

                <!-- In-App Notifications -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Pemberitahuan Terbaru</h3>
                    <div class="space-y-3">
                        @forelse($notifications as $notif)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="font-bold text-slate-800">{{ $notif->title }}</div>
                                <div class="text-slate-600 mt-1 leading-snug">{{ $notif->message }}</div>
                                <div class="text-[10px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 py-2 text-center">Tidak ada pemberitahuan baru.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection