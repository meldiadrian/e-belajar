@extends('layouts.app')

@section('title', 'Mengerjakan Kuis: ' . $attempt->quiz->title . ' - E-Belajar')

@section('content')
<div class="bg-slate-900 text-white py-4 px-4 sm:px-6 lg:px-8 sticky top-16 z-30 shadow-md">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <div>
            <span class="text-xs text-amber-400 font-bold uppercase tracking-wider">Percobaan #{{ $attempt->attempt_number }}</span>
            <h1 class="text-base sm:text-lg font-bold truncate">{{ $attempt->quiz->title }}</h1>
        </div>

        @if($attempt->quiz->time_limit > 0)
            <div class="flex items-center gap-2 bg-slate-800 px-3.5 py-1.5 rounded-xl border border-slate-700">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="quizTimer" class="font-mono text-sm font-bold text-amber-300">--:--</span>
            </div>
        @endif
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <form id="quizForm" action="{{ route('quiz.submit', $attempt->id) }}" method="POST">
        @csrf

        <div class="space-y-8">
            @foreach($quizData['questions'] as $qIndex => $question)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                            Soal {{ $qIndex + 1 }} dari {{ count($quizData['questions']) }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">Bobot: {{ $question['points'] }} Poin</span>
                    </div>

                    <div class="text-base font-bold text-slate-900 leading-relaxed">
                        {{ $question['question'] }}
                    </div>

                    <!-- Options list based on type -->
                    <div class="pt-3 space-y-2.5">
                        @if($question['type'] === 'single_choice' || $question['type'] === 'true_false')
                            @foreach($question['options'] as $option)
                                <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 cursor-pointer transition-all">
                                    <input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option['id'] }}" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-sm text-slate-800">{{ $option['option_text'] }}</span>
                                </label>
                            @endforeach
                        @elseif($question['type'] === 'multiple_choice')
                            <p class="text-xs text-amber-700 italic mb-2">* Pilih satu atau lebih jawaban yang benar</p>
                            @foreach($question['options'] as $option)
                                <label class="flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 cursor-pointer transition-all">
                                    <input type="checkbox" name="answers[{{ $question['id'] }}][]" value="{{ $option['id'] }}" class="w-4 h-4 text-emerald-600 rounded-sm focus:ring-emerald-500">
                                    <span class="text-sm text-slate-800">{{ $option['option_text'] }}</span>
                                </label>
                            @endforeach
                        @elseif($question['type'] === 'short_answer')
                            <input type="text" name="answers[{{ $question['id'] }}]" placeholder="Ketik jawaban singkat Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @elseif($question['type'] === 'essay')
                            <textarea name="answers[{{ $question['id'] }}]" rows="4" placeholder="Tuliskan uraian jawaban lengkap Anda di sini..." class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden"></textarea>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Button & Confirmation Bar -->
        <div class="mt-10 p-6 bg-white rounded-3xl border border-slate-200 shadow-md flex flex-col sm:flex-row justify-between items-center gap-4">
            <span class="text-xs text-slate-500">Pastikan seluruh butir pertanyaan telah Anda periksa sebelum mengirim lembar kuis.</span>
            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengirim jawaban kuis ini?')" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-2xl text-sm shadow-md transition-colors">
                Kirim Lembar Jawaban Kuis &rarr;
            </button>
        </div>
    </form>
</div>

@if($attempt->quiz->time_limit > 0)
<script>
    // Countdown Timer logic
    const limitMinutes = {{ $attempt->quiz->time_limit }};
    let remainingSeconds = limitMinutes * 60;
    const timerEl = document.getElementById('quizTimer');

    const interval = setInterval(() => {
        if (remainingSeconds <= 0) {
            clearInterval(interval);
            alert('Waktu pengerjaan kuis telah habis! Jawaban Anda akan otomatis dikirim.');
            document.getElementById('quizForm').submit();
            return;
        }

        remainingSeconds--;
        const m = Math.floor(remainingSeconds / 60);
        const s = remainingSeconds % 60;
        timerEl.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }, 1000);
</script>
@endif
@endsection
