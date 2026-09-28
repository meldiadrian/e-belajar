@extends('layouts.app')

@section('title', 'Hasil Evaluasi Kuis - ' . $attempt->quiz->title)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Score Summary Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden mb-8">

            @if($attempt->passed)
                {{-- KONDISI 1: LULUS --}}
                @if($certificate)
                    <div
                        class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-emerald-950">
                        <div class="flex items-center gap-3 text-xs">
                            <span
                                class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                            <div>
                                <div class="font-bold text-slate-900">Kuis Evaluasi Telah Lulus & Memenuhi Syarat Nilai.</div>
                                <div class="text-slate-600">Sertifikat resmi kelulusan telah diterbitkan dan dapat langsung diakses.</div>
                            </div>
                        </div>
                        <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}"
                            class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shrink-0 shadow-xs flex items-center gap-1.5">
                            <span>Buka Sertifikat</span>
                            &rarr;
                        </a>
                    </div>
                @endif

                <div class="p-8 sm:p-12 text-center bg-gradient-to-b from-emerald-900 via-emerald-950 to-slate-950 text-white">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Status: LULUS (Memenuhi Syarat Nilai)
                    </div>

                    <div class="text-6xl sm:text-7xl font-black mb-3 text-white tracking-tight">{{ $attempt->percentage }}%</div>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto">
                        Nilai Anda: <span class="font-bold text-white">{{ $attempt->score }}</span> dari total <span class="font-bold text-white">{{ $attempt->max_score }}</span> poin &bull; Standar Kelulusan: <span class="font-bold text-emerald-300">{{ $attempt->quiz->passing_score }}%</span>
                    </p>

                    @if($attempt->quiz->course)
                        <div class="mt-4">
                            <span class="inline-block text-xs sm:text-sm font-semibold text-emerald-200 bg-emerald-900/60 px-4 py-1 rounded-xl border border-emerald-700/40">
                                {{ $attempt->quiz->course->title }}
                            </span>
                        </div>
                    @endif

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        @if($certificate)
                            <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold text-xs sm:text-sm shadow-lg shadow-amber-400/20 transition-all hover:-translate-y-0.5">
                                <svg class="w-4 h-4 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Buka Lembar Sertifikat</span>
                                &rarr;
                            </a>
                        @endif

                        <a href="{{ route('learning.course', $attempt->quiz->course_id) }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm backdrop-blur-md transition-colors border border-white/15">
                            <span>Lanjutkan Pembelajaran</span>
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs sm:text-sm transition-colors border border-slate-700">
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

            @else
                {{-- KONDISI 2: TIDAK LULUS (BELUM MEMENUHI SYARAT NILAI) --}}
                <div class="px-6 py-4 bg-rose-50 border-b border-rose-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-rose-950">
                    <div class="flex items-center gap-3 text-xs">
                        <span
                            class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-xs shrink-0">!</span>
                        <div>
                            <div class="font-bold text-rose-900">Belum Memenuhi Syarat Nilai Kelulusan.</div>
                            <div class="text-rose-700">Sertifikat hanya dapat ditampilkan jika nilai kuis memenuhi syarat kelulusan minimal {{ $attempt->quiz->passing_score }}%.</div>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-rose-200 text-rose-900 text-[11px] font-bold shrink-0">
                        Sertifikat Terkunci
                    </span>
                </div>

                <div class="p-8 sm:p-12 text-center bg-gradient-to-b from-rose-950 via-slate-900 to-slate-950 text-white">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4 bg-rose-500/20 text-rose-300 border border-rose-400/30">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        Status: TIDAK LULUS (Belum Memenuhi Syarat)
                    </div>

                    <div class="text-6xl sm:text-7xl font-black mb-3 text-rose-400 tracking-tight">{{ $attempt->percentage }}%</div>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-md mx-auto">
                        Nilai Anda: <span class="font-bold text-white">{{ $attempt->score }}</span> / <span class="font-bold text-white">{{ $attempt->max_score }}</span> poin &bull; Syarat Nilai Kelulusan: <span class="font-bold text-rose-300">{{ $attempt->quiz->passing_score }}%</span>
                    </p>

                    @if($attempt->quiz->course)
                        <div class="mt-4">
                            <span class="inline-block text-xs sm:text-sm font-semibold text-slate-300 bg-slate-800/80 px-4 py-1 rounded-xl border border-slate-700">
                                {{ $attempt->quiz->course->title }}
                            </span>
                        </div>
                    @endif

                    <div class="mt-6 p-4 max-w-xl mx-auto rounded-2xl bg-rose-900/30 border border-rose-500/20 text-xs text-rose-200 leading-relaxed">
                        Nilai kuis Anda belum mencapai standar kelulusan. Anda dapat <strong>mengulangi kuis ini tanpa batas</strong> dan mempelajari kembali materi kelas sampai Anda berhasil lulus dan membuka sertifikat resmi.
                    </div>

                    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <form action="{{ route('quiz.attempt', $attempt->quiz->id) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs sm:text-sm shadow-lg shadow-amber-400/20 transition-all hover:-translate-y-0.5 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Ulangi Kuis Sekarang (Tanpa Batas)</span>
                                &rarr;
                            </button>
                        </form>

                        <a href="{{ route('learning.course', $attempt->quiz->course_id) }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm backdrop-blur-md transition-colors border border-white/15">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Pelajari Kembali Materi Kelas</span>
                        </a>

                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs sm:text-sm transition-colors border border-slate-700">
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Answers Breakdown & Review -->
            @if($attempt->answers && $attempt->answers->count() > 0)
                <div class="p-6 sm:p-8 space-y-6 bg-slate-50/50">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tinjauan Butir Jawaban Evaluasi:</h3>
                        <span class="text-xs font-semibold text-slate-500">{{ $attempt->answers->count() }} Butir Soal</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($attempt->answers as $index => $ans)
                            <div class="p-5 rounded-2xl border {{ $ans->is_correct ? 'border-emerald-200 bg-emerald-50/40' : 'border-rose-200 bg-rose-50/40' }} space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-slate-600">Pertanyaan {{ $index + 1 }}</span>
                                    <span class="font-bold {{ $ans->is_correct ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $ans->is_correct ? '✓ Benar' : '✗ Belum Tepat' }} (+{{ $ans->points_earned }} Poin)
                                    </span>
                                </div>

                                <div class="text-sm font-bold text-slate-900">{{ $ans->question->question }}</div>

                                @if($ans->question->explanation)
                                    <div class="p-3 bg-white/80 rounded-xl text-xs text-slate-600 border border-slate-200">
                                        <span class="font-bold text-slate-700 block mb-0.5">Penjelasan Materi:</span>
                                        {{ $ans->question->explanation }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection