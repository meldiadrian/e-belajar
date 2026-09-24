@extends('layouts.app')

@section('title', 'Katalog Kursus - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="bg-emerald-950 text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-900 to-slate-900 opacity-95"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Katalog Kursus Digital</h1>
        <p class="text-sm sm:text-base text-emerald-200 mt-2 max-w-2xl">
            Pilih kursus peningkatan kompetensi mandiri sesuai kebutuhan Anda. Semua kursus diselenggarakan secara daring dan bersertifikat resmi.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Search & Filter Controls -->
    <form method="GET" action="{{ route('courses.index') }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs mb-8 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Keyword -->
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Cari Kursus</label>
                <div class="relative">
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ketik judul kursus atau materi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                <select name="category" id="category" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden bg-white">
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
                <select name="sort" class="text-xs font-semibold px-2 py-1.5 rounded-lg border border-slate-300 bg-white" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Paling Banyak Peserta</option>
                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Abjad (A - Z)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                @if(request()->hasAny(['search', 'category', 'sort', 'tag']))
                    <a href="{{ route('courses.index') }}" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-red-800">Reset Filter</a>
                @endif
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($courses as $course)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-xl transition-all overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- Thumbnail -->
                    <div class="relative h-48 bg-gradient-to-br from-emerald-800 to-slate-900 flex items-center justify-center text-white overflow-hidden">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="text-center p-6">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-xl bg-white/10 flex items-center justify-center text-amber-400">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
                                </div>
                                <span class="text-xs font-semibold text-emerald-300 uppercase tracking-wider">{{ $course->category->name ?? 'E-Belajar' }}</span>
                            </div>
                        @endif



                        @if($course->certificate_enabled)
                            <div class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] font-bold text-amber-400 flex items-center gap-1 border border-amber-400/30">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Sertifikat</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="text-xs font-semibold text-emerald-700 mb-1">{{ $course->category->name ?? 'Umum' }}</div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-emerald-700 transition-colors line-clamp-2">
                            <a href="{{ route('courses.show', $course->slug ?? $course->id) }}">{{ $course->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ Str::limit($course->description, 110) }}</p>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-0 space-y-4">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>{{ $course->duration > 0 ? $course->duration . ' Menit' : 'Fleksibel' }}</span>
                        <span>{{ $course->lessons_count }} Pelajaran</span>
                        <span>{{ $course->enrollments_count }} Peserta</span>
                    </div>

                    <a href="{{ route('courses.show', $course->slug ?? $course->id) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl transition-all shadow-xs">
                        Lihat Silabus & Daftar &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 rounded-2xl border border-slate-200 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Kursus Tidak Ditemukan</h3>
                <p class="text-xs text-slate-500 mb-4">Coba sesuaikan kata kunci atau atur ulang filter pencarian Anda.</p>
                <a href="{{ route('courses.index') }}" class="px-4 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-lg hover:bg-emerald-100">Reset Semua Filter</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $courses->links() }}
    </div>
</div>
@endsection
