@extends('layouts.app')

@section('title', $course->title . ' - E-Belajar Kabupaten Bengkalis')

@section('content')
<!-- Header Header Detail -->
<div class="bg-slate-900 text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-900 opacity-95"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-400 mb-4">
            <a href="{{ route('courses.index') }}" class="hover:underline">Katalog</a>
            <span>/</span>
            <span>{{ $course->category->name ?? 'Umum' }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    @if($course->certificate_enabled)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Sertifikat Resmi Tersedia
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight">{{ $course->title }}</h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-3xl">{{ $course->description }}</p>

                <div class="flex flex-wrap items-center gap-6 pt-2 text-xs text-slate-400">
                    <div>Instruktur/Pengelola: <span class="text-white font-bold">{{ $course->creator->name ?? 'Admin Pemkab' }}</span></div>
                    <div>Durasi: <span class="text-white font-bold">{{ $course->duration > 0 ? $course->duration . ' Menit' : 'Fleksibel' }}</span></div>
                    <div>Modul: <span class="text-white font-bold">{{ $course->modules->count() }} Modul</span></div>
                </div>
            </div>

            <!-- Enrollment Card -->
            <div class="lg:col-span-4 bg-white text-slate-800 p-6 rounded-2xl shadow-xl border border-slate-200">
                <div class="text-center pb-4 border-b border-slate-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Akses Pembelajaran</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">100% GRATIS</div>
                    <div class="text-[11px] text-slate-500">Dibiayai APBD Kabupaten Bengkalis</div>
                </div>

                <div class="py-5 space-y-3 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Akses penuh materi modul & bahan ajar</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Evaluasi mandiri kuis pemahaman</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Sertifikat digital terverifikasi publik</span>
                    </div>
                </div>

                @auth
                    @if($isEnrolled)
                        <div class="space-y-3">
                            <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-xl text-center">
                                <span class="text-xs font-semibold text-emerald-800">Progres Anda:</span>
                                <div class="text-lg font-black text-emerald-700">{{ $courseProgress->progress_percentage ?? 0 }}%</div>
                                <div class="w-full bg-emerald-200 rounded-full h-2 mt-1.5 overflow-hidden">
                                    <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $courseProgress->progress_percentage ?? 0 }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('learning.course', $course->slug ?? $course->id) }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-md transition-all">
                                Lanjutkan Belajar Sekarang &rarr;
                            </a>
                        </div>
                    @else
                        <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3.5 text-center text-sm font-bold text-white bg-gradient-to-r from-emerald-700 to-teal-700 hover:from-emerald-800 hover:to-teal-800 rounded-xl shadow-lg transition-all">
                                Daftar Kursus Sekarang
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full py-3.5 text-center text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-md transition-all">
                        Masuk untuk Mendaftar
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Curriculum / Syllabus Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <div class="lg:col-span-8 space-y-6">
            <h2 class="text-xl font-extrabold text-slate-900">Silabus & Materi Kursus</h2>

            <div class="space-y-4">
                @forelse($course->modules as $index => $module)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <!-- Module Header -->
                        <div class="bg-slate-50 p-4 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">{{ $module->title }}</h3>
                                    <span class="text-[11px] text-slate-500">{{ $module->lessons->count() }} Pelajaran</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lessons List -->
                        <div class="divide-y divide-slate-100">
                            @forelse($module->lessons as $lIndex => $lesson)
                                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-xs">
                                            @if($lesson->lesson_type === 'video')
                                                <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                            @elseif($lesson->lesson_type === 'document')
                                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                            @elseif($lesson->lesson_type === 'quiz')
                                                <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 10-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                            @else
                                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-xs font-semibold text-slate-800">{{ $lesson->title }}</div>
                                            <div class="text-[10px] text-slate-400 capitalize">{{ $lesson->lesson_type }} &bull; {{ $lesson->duration > 0 ? round($lesson->duration / 60) . ' menit' : 'Fleksibel' }}</div>
                                        </div>
                                    </div>

                                    <div>
                                        @if($lesson->is_preview)
                                            <a href="{{ route('learning.lesson', [$course->id, $lesson->id]) }}" class="px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200">
                                                Pratinjau Gratis
                                            </a>
                                        @elseif($isEnrolled)
                                            <a href="{{ route('learning.lesson', [$course->id, $lesson->id]) }}" class="text-xs text-emerald-700 font-semibold hover:underline">
                                                Buka Materi &rarr;
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Terkunci
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-xs text-slate-400 text-center">Belum ada lesson pada modul ini.</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                        Silabus materi sedang dalam persiapan oleh pengelola.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4">Informasi Tambahan</h3>
                <div class="space-y-3 text-xs text-slate-600">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Penyelenggara</span>
                        <span class="font-bold text-slate-800">Pemkab Bengkalis</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Metode Belajar</span>
                        <span class="font-bold text-slate-800">100% Asynchronous Online</span>
                    </div>
                    <!-- <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-400">Passing Score Kuis</span>
                        <span class="font-bold text-slate-800">70% Minimum</span>
                    </div> -->
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-400">Legalitas Sertifikat</span>
                        <span class="font-bold text-emerald-700">Terdaftar & Sah</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
