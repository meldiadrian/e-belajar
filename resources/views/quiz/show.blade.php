@extends('layouts.app')

@section('title', 'Kuis: ' . $quiz->title . ' - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md p-8 space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 10-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">{{ $quiz->course->title }}</span>
                <h1 class="text-2xl font-black text-slate-900 leading-tight">{{ $quiz->title }}</h1>
            </div>
        </div>

        <p class="text-sm text-slate-600 leading-relaxed">{{ $quiz->description ?? 'Uji pemahaman Anda terhadap topik yang telah dipelajari.' }}</p>

        <!-- Rules & Specs -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 block mb-0.5">Jumlah Pertanyaan</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quiz->questions_count }} Butir</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Standar Kelulusan</span>
                <span class="font-bold text-emerald-700 text-sm">{{ $quiz->passing_score }}%</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Batas Waktu</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quiz->time_limit > 0 ? $quiz->time_limit . ' Menit' : 'Tidak Terbatas' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block mb-0.5">Batas Percobaan</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quiz->max_attempts > 0 ? $quiz->max_attempts . 'x' : 'Bebas' }}</span>
            </div>
        </div>

        <!-- Previous attempts table -->
        @if($userAttempts->count() > 0)
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Riwayat Percobaan Anda:</h3>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($userAttempts as $att)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-800">Percobaan ke-{{ $att->attempt_number }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $att->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-sm">{{ $att->percentage }}%</span>
                                <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ ($att->passed || $att->status === 'submitted') ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ($att->passed || $att->status === 'submitted') ? 'Lulus' : 'Sedang Dikerjakan' }}
                                </span>
                                @if($att->status === 'submitted')
                                    <a href="{{ route('quiz.result', $att->id) }}" class="text-emerald-700 font-bold hover:underline">Lihat Hasil &rarr;</a>
                                @else
                                    <a href="{{ route('quiz.take', $att->id) }}" class="px-2.5 py-1 bg-amber-400 text-slate-900 font-bold rounded-lg">Lanjutkan</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Button -->
        <div class="pt-4 flex items-center justify-between">
            <a href="javascript:history.back()" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali</a>

            @if($inProgressAttempt)
                <a href="{{ route('quiz.take', $inProgressAttempt->id) }}" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-md transition-colors">
                    Lanjutkan Mengerjakan Kuis &rarr;
                </a>
            @else
                <form action="{{ route('quiz.attempt', $quiz->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-md transition-colors">
                        Mulai Kerjakan Kuis Sekarang &rarr;
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
