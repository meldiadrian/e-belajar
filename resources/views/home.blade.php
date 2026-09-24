@extends('layouts.app')

@section('title', 'Beranda - E-Belajar Kabupaten Bengkalis')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-slate-900 text-white">
        <!-- Background Gradient Accent -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-slate-900 to-emerald-900 opacity-90"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-amber-500/10 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-800/60 border border-emerald-600/40 text-emerald-300 text-xs font-semibold backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Platform Digital Resmi Pemerintah Kabupaten Bengkalis</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                        Tingkatkan Kompetensi Mandiri Bersama <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">E-Belajar
                            Bengkalis</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                        Akses materi kurikulum terstruktur, video pembelajaran interaktif, asesmen kuis mandiri, dan peroleh
                        sertifikat kelulusan digital resmi dari Pemerintah Kabupaten Bengkalis.
                    </p>

                    <!-- <div class="flex flex-wrap items-center gap-4 pt-4">
                                                                            <a href="{{ route('courses.index') }}"
                                                                                class="px-6 py-3.5 rounded-xl font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-700/30 hover:shadow-emerald-700/50 transition-all flex items-center gap-2">
                                                                                <span>Lihat Katalog</span>
                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                                </svg>
                                                                            </a>
                                                                        </div> -->
                </div>

                <div class="lg:col-span-5">
                    <div
                        class="relative bg-slate-800/60 border border-slate-700/80 p-8 rounded-3xl shadow-2xl backdrop-blur-xl">
                        <div class="text-xs font-bold text-amber-400 tracking-wider uppercase mb-1">E-Belajar Kabupaten
                            Bengkalis
                        </div>
                        <h3 class="text-xl font-bold text-white mb-4">Lihat Cara Menggunakan</h3>
                        <p class="text-xs text-slate-300 mb-6">
                            Tonton untuk mempelajari cara mengakses dan menggunakan E-Belajar untuk membantu ASN bekerja
                            lebih
                            efisien dalam memberikan pelayanan terbaik kepada seluruh masyarakat Indonesia.
                        </p>

                        <form id="verifyForm"
                            onsubmit="event.preventDefault(); window.location.href='/certificates/verify/' + document.getElementById('certCodeInput').value.trim();"
                            class="space-y-4">

                            <button type="submit"
                                class="w-full py-3 px-4 rounded-xl font-bold text-emerald-950 bg-amber-400 hover:bg-amber-300 transition-colors shadow-md text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span>Lihat Cara Menggunakan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Counter -->
    <!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_students']) }}</div>
                                            <div class="text-xs font-semibold text-slate-500">Peserta Terdaftar</div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-black">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_courses']) }}</div>
                                            <div class="text-xs font-semibold text-slate-500">Kursus Tersedia</div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_enrollments']) }}</div>
                                            <div class="text-xs font-semibold text-slate-500">Total Enrollment</div>
                                        </div>
                                    </div>

                                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-md flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-2xl font-black text-slate-800">{{ number_format($stats['total_certificates']) }}</div>
                                            <div class="text-xs font-semibold text-slate-500">Sertifikat Terbit</div>
                                        </div>
                                    </div>
                                </div>
                            </section> -->

    <!-- Categories Section -->
    <!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                            <div class="flex justify-between items-end mb-8">
                                <div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Kategori Program</span>
                                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Bidang Keahlian Pembelajaran</h2>
                                </div>
                                <a href="{{ route('courses.index') }}"
                                    class="text-sm font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                                    <span>Lihat Semua Kursus</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @forelse($categories as $category)
                                    <a href="{{ route('courses.index', ['category' => $category->slug]) }}"
                                        class="group bg-white p-5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:shadow-md transition-all text-center">
                                        <div
                                            class="w-12 h-12 mx-auto rounded-xl bg-emerald-50 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center font-bold text-lg mb-3 transition-colors">
                                            {{ substr($category->name, 0, 1) }}
                                        </div>
                                        <div class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors">
                                            {{ $category->name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-1">{{ $category->courses_count }} Kursus</div>
                                    </a>
                                @empty
                                    <div class="col-span-5 text-center py-6 text-slate-400">Belum ada kategori program yang aktif.</div>
                                @endforelse
                            </div>
                        </section> -->

    <!-- Featured Courses Section -->
    <!-- <section class="bg-slate-100/70 border-y border-slate-200 py-16">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-end mb-8">
                                <div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest">Pilihan Terbaik</span>
                                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Kursus Unggulan Terbaru</h2>
                                </div>
                                <a href="{{ route('courses.index') }}"
                                    class="text-sm font-semibold text-emerald-700 hover:underline">Telusuri Semua &rarr;</a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                @forelse($featuredCourses as $course)
                                    <div
                                        class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-xl transition-all overflow-hidden flex flex-col group"> -->
    <!-- Thumbnail header -->
    <!-- <div
                                            class="relative h-48 bg-gradient-to-br from-emerald-800 to-slate-900 flex items-center justify-center text-white overflow-hidden">
                                            @if($course->thumbnail)
                                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="text-center p-6">
                                                    <div
                                                        class="w-12 h-12 mx-auto mb-2 rounded-xl bg-white/10 flex items-center justify-center text-amber-400">
                                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
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
                                                    <span>Sertifikat Resmi</span>
                                                </div>
                                            @endif
                                        </div> -->

    <!-- Body -->
    <!-- <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                            <div>
                                                <div class="text-xs font-semibold text-emerald-700 mb-1">{{ $course->category->name ?? 'Umum' }}
                                                </div>
                                                <h3
                                                    class="text-lg font-bold text-slate-900 leading-snug group-hover:text-emerald-700 transition-colors line-clamp-2">
                                                    <a href="{{ route('courses.show', $course->slug ?? $course->id) }}">{{ $course->title }}</a>
                                                </h3>
                                                <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ Str::limit($course->description, 100) }}
                                                </p>
                                            </div>

                                            <div
                                                class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>{{ $course->duration > 0 ? $course->duration . ' Menit' : 'Fleksibel' }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                    <span>{{ $course->lessons_count }} Pelajaran</span>
                                                </div>
                                            </div>

                                            <a href="{{ route('courses.show', $course->slug ?? $course->id) }}"
                                                class="block w-full py-2.5 text-center text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-700 hover:text-white rounded-xl transition-colors">
                                                Lihat Detail & Mulai Belajar &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-3 text-center py-12 text-slate-400">Belum ada kursus yang diterbitkan saat ini.</div>
                                @endforelse
                            </div>
                        </div>
                    </section> -->
@endsection