@extends('layouts.app')

@section('title', 'Hasil Evaluasi Kuis - ' . $attempt->quiz->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Score Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden mb-8">
        <div class="p-8 text-center bg-gradient-to-b from-emerald-800 to-emerald-950 text-white">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 bg-emerald-500/30 text-emerald-200 border border-emerald-400/40">
                Status: LULUS
            </div>

            <div class="text-5xl sm:text-6xl font-black mb-2">{{ $attempt->percentage }}%</div>
            <p class="text-xs text-slate-300">
                Total Nilai: <span class="font-bold text-white">{{ $attempt->score }}</span> dari maksimal <span class="font-bold text-white">{{ $attempt->max_score }}</span> poin &bull; Status Evaluasi: <span class="font-bold text-emerald-300">Lulus</span>
            </p>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                @if($certificate)
                    <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}" class="px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-bold text-xs hover:bg-amber-300 transition-colors shadow-md flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Buka Sertifikat Kelulusan &rarr;</span>
                    </a>
                @elseif($attempt->quiz->course && $attempt->quiz->course->certificate_enabled)
                    <a href="{{ route('my.certificates') }}" class="px-5 py-2.5 rounded-xl bg-amber-400 text-slate-950 font-bold text-xs hover:bg-amber-300 transition-colors shadow-md flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-950" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Buka Lembar Sertifikat &rarr;</span>
                    </a>
                @endif
                <a href="{{ route('learning.course', $attempt->quiz->course_id) }}" class="px-5 py-2.5 rounded-xl bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 transition-colors shadow-sm">
                    &larr; Lanjutkan Pembelajaran
                </a>
            </div>
        </div>

        @if($certificate || ($attempt->quiz->course && $attempt->quiz->course->certificate_enabled))
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-emerald-950">
                <div class="flex items-center gap-3 text-xs">
                    <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">✓</span>
                    <div>
                        <div class="font-bold text-slate-900">Kuis Evaluasi Telah Diselesaikan.</div>
                        <div class="text-slate-600">Sertifikat resmi kelulusan telah diterbitkan dan dapat langsung diakses.</div>
                    </div>
                </div>
                @if($certificate)
                    <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shrink-0 shadow-xs flex items-center gap-1.5">
                        <span>Buka Sertifikat</span>
                        &rarr;
                    </a>
                @else
                    <a href="{{ route('my.certificates') }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shrink-0 shadow-xs flex items-center gap-1.5">
                        <span>Buka Sertifikat</span>
                        &rarr;
                    </a>
                @endif
            </div>
        @endif

        <!-- Answers Breakdown -->
        <div class="p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tinjauan Butir Jawaban:</h3>

            <div class="space-y-4">
                @foreach($attempt->answers as $index => $ans)
                    <div class="p-5 rounded-2xl border {{ $ans->is_correct ? 'border-emerald-200 bg-emerald-50/30' : 'border-red-200 bg-red-50/30' }} space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600">Pertanyaan {{ $index + 1 }}</span>
                            <span class="font-bold {{ $ans->is_correct ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $ans->is_correct ? '✓ Benar' : '✗ Salah' }} (+{{ $ans->points_earned }} Poin)
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
    </div>
</div>
@endsection
