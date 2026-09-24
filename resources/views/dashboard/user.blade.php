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
                <span class="text-xs text-slate-500 font-semibold block mb-1">Pembelajaran Diikuti</span>
                <div class="text-2xl font-black text-slate-800">{{ $stats['total_enrolled'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Sedang Berjalan</span>
                <div class="text-2xl font-black text-amber-600">{{ $stats['in_progress'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Pembelajaran Selesai</span>
                <div class="text-2xl font-black text-emerald-600">{{ $stats['completed_courses'] }}</div>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <span class="text-xs text-slate-500 font-semibold block mb-1">Sertifikat</span>
                <div class="text-2xl font-black text-amber-600">{{ $stats['total_certificates'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Column: Enrolled Courses & Progress -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Active Courses -->
                <!-- <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
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
                                                                </div> -->

                @php
                    $courses = $courses ?? \App\Models\Course::where('status', 'published')->with(['category', 'creator', 'tags'])->withCount(['lessons', 'enrollments'])->paginate(6);
                    $categories = $categories ?? \App\Models\Category::where('is_active', true)->withCount('courses')->get();
                @endphp

                <!-- Search & Filter Controls -->
                <form method="GET" action="{{ route('dashboard') }}"
                    class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search Keyword -->
                        <div class="md:col-span-2">
                            <label for="search"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari
                                Pelatihan</label>
                            <div class="relative">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Ketik judul pelatihan atau materi..."
                                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                            <select name="category" id="category"
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden bg-white">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }} ({{ $cat->courses_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-semibold">Urutkan:</span>
                            <select name="sort"
                                class="text-xs font-semibold px-2 py-1.5 rounded-lg border border-slate-300 bg-white"
                                onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Paling Banyak
                                    Peserta</option>
                                <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Abjad (A - Z)
                                </option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(request()->hasAny(['search', 'category', 'sort', 'tag']))
                                <a href="{{ route('dashboard') }}"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-red-800">Reset Filter</a>
                            @endif
                            <button type="submit"
                                class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs">
                                Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Courses Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($courses as $course)
                        <div
                            class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-xl transition-all overflow-hidden flex flex-col justify-between group">
                            <div>
                                <!-- Thumbnail -->
                                <div
                                    class="relative h-48 bg-gradient-to-br from-emerald-800 to-slate-900 flex items-center justify-center text-white overflow-hidden">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="text-center p-6">
                                            <div
                                                class="w-12 h-12 mx-auto mb-2 rounded-xl bg-white/10 flex items-center justify-center text-amber-400">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
                                                </svg>
                                            </div>
                                            <span
                                                class="text-xs font-semibold text-emerald-300 uppercase tracking-wider">{{ $course->category->name ?? 'E-Belajar' }}</span>
                                        </div>
                                    @endif

                                    @if($course->certificate_enabled)
                                        <div
                                            class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-amber-400 flex items-center gap-1 border border-amber-400/30">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>Sertifikat</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-6">
                                    <div class="text-xs font-semibold text-emerald-700 mb-1">
                                        {{ $course->category->name ?? 'Umum' }}
                                    </div>
                                    <h3
                                        class="text-base font-bold text-slate-900 leading-snug group-hover:text-emerald-700 transition-colors line-clamp-2">
                                        <a
                                            href="{{ route('courses.show', $course->slug ?? $course->id) }}">{{ $course->title }}</a>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                        {{ Str::limit($course->description, 110) }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-6 pb-6 pt-0 space-y-4">
                                <div
                                    class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <span>{{ $course->duration > 0 ? $course->duration . ' Menit' : 'Fleksibel' }}</span>
                                    <span>{{ $course->lessons_count }} Pelajaran</span>
                                    <span>{{ $course->enrollments_count }} Peserta</span>
                                </div>

                                @php
                                    $isEnrolledInThis = $enrollments->pluck('course_id')->contains($course->id);
                                @endphp
                                @if($isEnrolledInThis)
                                    <a href="{{ route('learning.course', $course->slug ?? $course->id) }}"
                                        class="block w-full py-2.5 text-center text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl transition-all shadow-xs">
                                        Lanjutkan Belajar &rarr;
                                    </a>
                                @else
                                    <a href="{{ route('courses.show', $course->slug ?? $course->id) }}"
                                        class="block w-full py-2.5 text-center text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl transition-all shadow-xs">
                                        Lihat Silabus & Daftar &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white p-12 rounded-2xl border border-slate-200 text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 mb-1">Pembelajaran Tidak Ditemukan</h3>
                            <p class="text-xs text-slate-500 mb-4">Coba sesuaikan kata kunci atau atur ulang filter pencarian
                                Anda.</p>
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100">Reset
                                Semua Filter</a>
                        </div>
                    @endforelse
                </div>

                @if(method_exists($courses, 'hasPages') && $courses->hasPages())
                    <div class="mt-4">
                        {{ $courses->links() }}
                    </div>
                @endif

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
                            {{ $lastLessonProgress->lesson->module->course->title ?? '' }}
                        </p>
                        <a href="{{ route('learning.lesson', [$lastLessonProgress->lesson->module->course_id, $lastLessonProgress->lesson_id]) }}"
                            class="mt-3 inline-block px-4 py-2 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800 transition-colors">
                            Buka Materi Kembali &rarr;
                        </a>
                    </div>
                @endif

                <!-- Last Quiz Attempt -->
                <!-- @if($lastQuizAttempt)
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
                    @endif -->

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
                            <div class="text-xs text-slate-400 py-3 text-center">Selesaikan pembelajaran 100% untuk memperoleh
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