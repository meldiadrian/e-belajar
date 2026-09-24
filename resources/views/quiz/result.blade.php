@extends('layouts.app')

@section('title', 'Hasil Evaluasi Kuis - ' . $attempt->quiz->title)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Score Summary Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden mb-8">

            @if($certificate || ($attempt->quiz->course && $attempt->quiz->course->certificate_enabled))
                <div
                    class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-emerald-950">
                    <div class="flex items-center gap-3 text-xs">
                        <span
                            class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                        <div>
                            <div class="font-bold text-slate-900">Kuis Evaluasi Telah Diselesaikan.</div>
                            <div class="text-slate-600">Sertifikat resmi kelulusan telah diterbitkan dan dapat langsung diakses.
                            </div>
                        </div>
                    </div>
                    @if($certificate)
                        <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}"
                            class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shrink-0 shadow-xs flex items-center gap-1.5">
                            <span>Buka Sertifikat</span>
                            &rarr;
                        </a>
                    @else
                        <a href="{{ route('my.certificates') }}"
                            class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shrink-0 shadow-xs flex items-center gap-1.5">
                            <span>Buka Sertifikat</span>
                            &rarr;
                        </a>
                    @endif
                </div>
            @endif

            <!-- Congratulations & Completion Card -->
            <div class="p-8 sm:p-14 text-center">
                <div class="relative inline-flex items-center justify-center mb-6">
                    <div class="absolute inset-0 rounded-full bg-emerald-400/20 blur-xl"></div>
                    <div
                        class="relative w-20 h-20 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-600/25">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <span
                        class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        🎉 Pembelajaran Telah Selesai
                    </span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                    Selamat Telah Selesai Mengikuti Pembelajaran Ini!
                </h2>

                @if($attempt->quiz->course)
                    <div class="mt-2">
                        <span
                            class="inline-block text-sm sm:text-base font-semibold text-emerald-700 bg-emerald-50 px-4 py-1.5 rounded-xl border border-emerald-100">
                            {{ $attempt->quiz->course->title }}
                        </span>
                    </div>
                @endif

                <p class="text-slate-600 max-w-xl mx-auto text-sm sm:text-base leading-relaxed mt-4">
                    Kerja luar biasa! Anda telah berhasil menuntaskan seluruh rangkaian materi belajar dan evaluasi kuis
                    dengan sukses. Teruslah mengasah keterampilan dan tingkatkan potensi diri Anda ke tingkat berikutnya.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    @if($certificate)
                        <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-700/20 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Buka Lembar Sertifikat</span>
                            &rarr;
                        </a>
                    @elseif($attempt->quiz->course && $attempt->quiz->course->certificate_enabled)
                        <a href="{{ route('my.certificates') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-700/20 transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Buka Sertifikat</span>
                            &rarr;
                        </a>
                    @endif

                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('learning.course', $attempt->quiz->course_id) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Lihat Pembelajaran</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection