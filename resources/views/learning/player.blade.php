@extends('layouts.app')

@section('title', $lesson->title . ' - Ruang Belajar E-Belajar Bengkalis')

@section('content')
    <div class="bg-slate-900 text-white border-b border-slate-800 py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-2 text-xs text-emerald-400 font-semibold mb-1">
                    <a href="{{ route('my.courses') }}" class="hover:underline">&larr; Kembali ke Kursus Saya</a>
                    <span>&bull;</span>
                    <span class="text-slate-400">{{ $course->title }}</span>
                </div>
                <h1 class="text-lg sm:text-xl font-bold text-white">{{ $lesson->title }}</h1>
            </div>

            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                <div class="text-right">
                    <div class="text-[11px] text-slate-400">Kemajuan Kursus</div>
                    <div class="text-xs font-bold text-emerald-400">{{ $courseProgress->progress_percentage ?? 0 }}% Selesai
                    </div>
                </div>
                <div class="w-24 sm:w-32 bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-700">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $courseProgress->progress_percentage ?? 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

@php
    $videoContent = $lesson->contents->where('type', 'video')->first();
    $isYoutube = $videoContent && $videoContent->url && (str_contains($videoContent->url, 'youtube.com') || str_contains($videoContent->url, 'youtu.be'));
    $hasPlayableVideo = $lesson->lesson_type === 'video' && $videoContent && !empty($videoContent->url);
    $isCompleted = ($currentLessonProgress->status ?? '') === 'completed';
    $waitForVideo = $hasPlayableVideo && !$isCompleted;
@endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Main Multimedia / Content Area -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden">
                    <!-- Video Player Content -->
                    @if($lesson->lesson_type === 'video')
                        <div class="bg-black aspect-video flex items-center justify-center text-white relative">
                            @if($videoContent && $videoContent->url)
                                @if($isYoutube)
                                    @php
                                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoContent->url, $match);
                                        $ytId = $match[1] ?? '';
                                    @endphp
                                    <iframe id="lessonVideoIframe" class="w-full h-full" src="https://www.youtube.com/embed/{{ $ytId }}?enablejsapi=1" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                                @else
                                    <video id="lessonVideoPlayer" controls controlsList="nodownload noplaybackrate" oncontextmenu="return false;" class="w-full h-full">
                                        <source src="{{ $videoContent->url }}" type="video/mp4">
                                        Browser Anda tidak mendukung pemutar video.
                                    </video>
                                @endif
                            @else
                                <div class="text-center p-8">
                                    <div
                                        class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-900/60 flex items-center justify-center text-emerald-400">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-400">Video materi pembelajaran siap ditonton.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Document Reader Content -->
                    @elseif($lesson->lesson_type === 'document')
                        @php
                            $docContent = $lesson->contents->where('type', 'document')->first();
                        @endphp
                        <div class="p-8 bg-slate-50 border-b border-slate-200">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-black">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">
                                        {{ $docContent->title ?? 'Dokumen Bahan Ajar: ' . $lesson->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Format dokumen resmi pendukung pelatihan</p>
                                </div>
                            </div>

                            @if($docContent && $docContent->file_path)
                                <div class="mt-6 flex gap-3">
                                    <a href="{{ asset('storage/' . $docContent->file_path) }}" target="_blank"
                                        class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Unduh / Buka Dokumen Lengkap</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Quiz Prompt Content -->
                    @elseif($lesson->lesson_type === 'quiz')
                        <div class="p-8 bg-amber-50/60 border-b border-amber-200">
                            <div class="max-w-xl">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Evaluasi
                                    Pemahaman</span>
                                <h2 class="text-xl font-bold text-slate-900 mt-1">Uji Kompetensi Pembelajaran</h2>
                                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                                    Selesaikan kuis ini untuk menguji pemahaman materi yang telah dipelajari. Nilai kelulusan
                                    akan diperhitungkan untuk penerbitan sertifikat resmi.
                                </p>

                                @if($lesson->quizzes->count() > 0)
                                    <div class="mt-6">
                                        <a href="{{ route('quiz.show', $lesson->quizzes->first()->id) }}"
                                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-md transition-colors">
                                            <span>Buka Lembar Kuis Sekarang</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Text Content & Notes -->
                    <div class="p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Modul:
                                    {{ $lesson->module->title }}</span>
                                <h2 class="text-xl font-extrabold text-slate-900 mt-0.5">{{ $lesson->title }}</h2>
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ ($currentLessonProgress->status ?? '') === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ ($currentLessonProgress->status ?? '') === 'completed' ? '✓ Telah Selesai' : 'Sedang Dipelajari' }}
                            </span>
                        </div>

                        <div class="prose max-w-none text-sm text-slate-700 leading-relaxed space-y-4">
                            {!! nl2br(e($lesson->content ?? 'Materi teks pembelajaran mandiri. Silakan baca dan pelajari instruksi yang tertera.')) !!}
                        </div>

                        <!-- Additional Content Attachments -->
                        @if($lesson->contents->count() > 0)
                            <div class="mt-8 pt-6 border-t border-slate-100">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Lampiran & Bahan Ajar
                                    Terkait:</h4>
                                <div class="space-y-2">
                                    @foreach($lesson->contents as $content)
                                        <div
                                            class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px]">
                                                    {{ strtoupper(substr($content->type, 0, 1)) }}
                                                </span>
                                                <span class="font-semibold text-slate-800">{{ $content->title }}</span>
                                            </div>
                                            @if($content->url)
                                                <a href="{{ $content->url }}" target="_blank"
                                                    class="text-emerald-700 font-bold hover:underline">Buka Tautan &rarr;</a>
                                            @elseif($content->file_path)
                                                <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank"
                                                    class="text-emerald-700 font-bold hover:underline">Unduh File &rarr;</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Footer Action Bar -->
                    <div
                        class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div>
                            @if($prevLesson)
                                <a href="{{ route('learning.lesson', [$course->id, $prevLesson->id]) }}"
                                    class="px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 hover:bg-white transition-colors flex items-center gap-1.5">
                                    &larr; Sebelumnya: {{ Str::limit($prevLesson->title, 20) }}
                                </a>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            @if($waitForVideo)
                                <div id="videoDurationNotice" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium shadow-xs">
                                    <svg class="w-4 h-4 text-amber-600 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>
                                        Video tidak dapat dipercepat. Sisa waktu: 
                                        <span id="videoRemainingCounter" class="font-mono font-bold text-amber-950 bg-amber-100 px-2 py-0.5 rounded-md">
                                            @if($lesson->duration > 0)
                                                {{ sprintf('%02d:%02d', floor($lesson->duration / 60), $lesson->duration % 60) }}
                                            @else
                                                --:--
                                            @endif
                                        </span>
                                    </span>
                                </div>
                            @endif

                            <div id="videoWarningToast" class="fixed bottom-6 right-6 z-50 hidden bg-slate-900/95 text-white text-xs px-4 py-3 rounded-2xl shadow-2xl border border-amber-500/50 flex items-center gap-2.5 transition-all duration-300">
                                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span id="videoWarningToastText">Video tidak dapat dipercepat atau dilompati ke depan.</span>
                            </div>

                            <form id="completeBtnForm" action="{{ route('learning.lesson.complete', $lesson->id) }}" method="POST" class="{{ $waitForVideo ? 'hidden' : 'inline-flex' }}">
                                @csrf
                                <button type="submit"
                                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md transition-all flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $isCompleted ? 'Telah Selesai (Simpan Ulang)' : 'Tandai Selesai' }}</span>
                                </button>
                            </form>

                            @if($lessonQuiz && !$lessonQuizPassed)
                                <a href="{{ route('quiz.show', $lessonQuiz->id) }}"
                                    class="px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Buka Kuis Materi &rarr;</span>
                                </a>
                            @endif

                            @if($nextLesson)
                                <a href="{{ route('learning.lesson', [$course->id, $nextLesson->id]) }}"
                                    class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors flex items-center gap-1.5">
                                    <span>Selanjutnya: {{ Str::limit($nextLesson->title, 20) }}</span>
                                    &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 100% Completion or Quiz/Certificate Prompt -->
                @if(($courseProgress->progress_percentage ?? 0) >= 100)
                    @if($courseQuiz && !$courseQuizPassed)
                        <!-- Quiz Prompt Card: Selesai materi, tampil kuis -->
                        <div
                            class="p-6 rounded-3xl bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/30 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-7 h-7 text-amber-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-amber-950/70">Materi Pelajaran Telah Selesai (100%)</div>
                                    <div class="text-lg font-black">Lanjutkan ke Kuis Evaluasi Pemahaman</div>
                                    <p class="text-xs text-amber-950/80 mt-0.5">Selesaikan kuis evaluasi kelulusan untuk membuka sertifikat resmi Anda.</p>
                                </div>
                            </div>
                            <a href="{{ route('quiz.show', $courseQuiz->id) }}"
                                class="px-5 py-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-md shrink-0 flex items-center gap-2">
                                <span>Kerjakan Kuis Sekarang</span>
                                &rarr;
                            </a>
                        </div>
                    @elseif($course->certificate_enabled && ($courseQuizPassed || !$courseQuiz))
                        <!-- Certificate Card: Tampil setelah kuis selesai dan lulus -->
                        <div
                            class="p-6 rounded-3xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center font-black shrink-0">
                                    <svg class="w-7 h-7 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-wider text-emerald-200">Selamat! Seluruh Materi & Evaluasi Selesai</div>
                                    <div class="text-lg font-black">Sertifikat Kelulusan Resmi Anda Siap Dibuka</div>
                                </div>
                            </div>
                            @if($certificate)
                                <a href="{{ route('certificates.show', $certificate->certificate_code ?? $certificate->id) }}"
                                    class="px-5 py-3 rounded-xl bg-amber-400 text-slate-950 text-xs font-bold hover:bg-amber-300 transition-colors shadow-md shrink-0 flex items-center gap-2">
                                    <span>Buka Sertifikat Kelulusan</span>
                                    &rarr;
                                </a>
                            @else
                                <a href="{{ route('my.certificates') }}"
                                    class="px-5 py-3 rounded-xl bg-white text-slate-900 text-xs font-bold hover:bg-slate-100 transition-colors shadow-md shrink-0 flex items-center gap-2">
                                    <span>Buka Lembar Sertifikat</span>
                                    &rarr;
                                </a>
                            @endif
                        </div>
                    @endif
                @endif
            </div>

            <!-- Right Syllabus Accordion Sidebar -->
            <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Daftar Modul & Pelajaran</h3>

                <div class="space-y-4">
                    @foreach($course->modules as $modIndex => $module)
                        <div class="border border-slate-200 rounded-2xl overflow-hidden">
                            <div class="bg-slate-50 p-3.5 border-b border-slate-200 flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800">Modul {{ $modIndex + 1 }}:
                                    {{ $module->title }}</span>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @foreach($module->lessons as $les)
                                    @php
                                        $p = $lessonProgressMap->get($les->id);
                                        $isCompleted = ($p && $p->status === 'completed');
                                        $isCurrent = ($les->id === $lesson->id);
                                    @endphp
                                    <a href="{{ route('learning.lesson', [$course->id, $les->id]) }}"
                                        class="p-3 flex items-center justify-between text-xs transition-colors {{ $isCurrent ? 'bg-emerald-50 text-emerald-900 font-bold border-l-4 border-emerald-600' : 'hover:bg-slate-50 text-slate-700' }}">
                                        <div class="flex items-center gap-2 truncate pr-2">
                                            <div
                                                class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 text-[10px] {{ $isCompleted ? 'bg-emerald-600 text-white' : ($isCurrent ? 'border-2 border-emerald-600 text-emerald-600' : 'border border-slate-300 text-slate-400') }}">
                                                @if($isCompleted)
                                                    ✓
                                                @else
                                                    &bull;
                                                @endif
                                            </div>
                                            <span class="truncate">{{ $les->title }}</span>
                                        </div>
                                        <span class="text-[10px] text-slate-400 uppercase shrink-0">{{ $les->lesson_type }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($courseQuiz)
                    <div class="pt-2 border-t border-slate-200">
                        <div class="p-3.5 rounded-2xl border {{ $courseQuizPassed ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50' }} flex items-center justify-between">
                            <div class="truncate pr-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $courseQuizPassed ? 'text-emerald-700' : 'text-amber-800' }}">Kuis Evaluasi</span>
                                <div class="text-xs font-bold text-slate-900 truncate">{{ $courseQuiz->title }}</div>
                            </div>
                            <a href="{{ route('quiz.show', $courseQuiz->id) }}" class="px-2.5 py-1.5 rounded-lg text-xs font-bold shrink-0 {{ $courseQuizPassed ? 'bg-emerald-700 text-white hover:bg-emerald-800' : 'bg-amber-500 text-slate-950 hover:bg-amber-400' }}">
                                {{ $courseQuizPassed ? 'Lihat Hasil' : 'Mulai Kuis' }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let toastTimeout = null;
    function showVideoWarning(msg) {
        const toast = document.getElementById('videoWarningToast');
        const toastText = document.getElementById('videoWarningToastText');
        if (!toast) return;
        if (toastText && msg) toastText.textContent = msg;
        toast.classList.remove('hidden');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    function formatTime(seconds) {
        if (isNaN(seconds) || seconds < 0) return '00:00';
        const total = Math.floor(seconds);
        const m = Math.floor(total / 60);
        const s = total % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    function updateCounter(remainingSeconds) {
        const counterEl = document.getElementById('videoRemainingCounter');
        if (counterEl) {
            counterEl.textContent = formatTime(remainingSeconds);
        }
    }

    function handleVideoFinished() {
        const notice = document.getElementById('videoDurationNotice');
        const form = document.getElementById('completeBtnForm');
        if (notice) {
            notice.style.display = 'none';
        }
        if (form) {
            form.classList.remove('hidden');
            form.classList.add('inline-flex');
        }
    }

    // HTML5 Native Video Player Protection
    const video = document.getElementById('lessonVideoPlayer');
    if (video) {
        let maxTimeWatched = 0;

        video.addEventListener('loadedmetadata', function() {
            if (video.duration && !isNaN(video.duration)) {
                updateCounter(video.duration);
            }
        });

        video.addEventListener('timeupdate', function() {
            if (!video.seeking) {
                if (video.currentTime > maxTimeWatched) {
                    // Natural progression: allow small increment (up to 1.5s delta)
                    if (video.currentTime - maxTimeWatched <= 1.5) {
                        maxTimeWatched = video.currentTime;
                    } else {
                        // User tried jumping forward without seeking event
                        video.currentTime = maxTimeWatched;
                        showVideoWarning('Video tidak dapat dipercepat atau dilompati.');
                    }
                }
            }

            if (video.duration && !isNaN(video.duration)) {
                const remaining = Math.max(0, video.duration - maxTimeWatched);
                updateCounter(remaining);

                if (maxTimeWatched >= (video.duration - 1) || video.ended) {
                    handleVideoFinished();
                }
            }
        });

        // Block seeking forward past the maximum watched timestamp
        video.addEventListener('seeking', function() {
            if (video.currentTime > maxTimeWatched) {
                video.currentTime = maxTimeWatched;
                showVideoWarning('Video tidak dapat dipercepat atau dilompati ke depan.');
            }
        });

        // Enforce normal playback speed (cannot be set higher than 1.0x)
        video.addEventListener('ratechange', function() {
            if (video.playbackRate > 1.0) {
                video.playbackRate = 1.0;
                showVideoWarning('Kecepatan pemutaran dibatasi maksimal 1.0x.');
            }
        });

        // Block keyboard shortcuts for seeking forward
        video.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight' || e.key === 'l' || e.key === 'L') {
                e.preventDefault();
                showVideoWarning('Pintasan percepat video dinonaktifkan.');
            }
        });

        video.addEventListener('ended', function() {
            if (video.duration && maxTimeWatched >= (video.duration - 2)) {
                handleVideoFinished();
            } else {
                video.currentTime = maxTimeWatched;
                showVideoWarning('Harap tonton materi video sampai selesai.');
            }
        });
    }

    @if($isYoutube)
        var ytScript = document.createElement('script');
        ytScript.src = "https://www.youtube.com/iframe_api";
        var firstScript = document.getElementsByTagName('script')[0];
        if (firstScript) {
            firstScript.parentNode.insertBefore(ytScript, firstScript);
        } else {
            document.head.appendChild(ytScript);
        }

        var ytPlayer;
        var ytMaxWatched = 0;
        var ytInterval = null;

        function startYtMonitor() {
            if (ytInterval) clearInterval(ytInterval);
            ytInterval = setInterval(function() {
                if (!ytPlayer || typeof ytPlayer.getCurrentTime !== 'function') return;
                var current = ytPlayer.getCurrentTime();
                var duration = ytPlayer.getDuration();

                if (duration > 0) {
                    var remaining = Math.max(0, duration - ytMaxWatched);
                    updateCounter(remaining);
                }

                // Enforce playback rate <= 1.0
                if (typeof ytPlayer.getPlaybackRate === 'function' && ytPlayer.getPlaybackRate() > 1.0) {
                    ytPlayer.setPlaybackRate(1.0);
                    showVideoWarning('Kecepatan video dibatasi maksimal 1.0x.');
                }

                // If user jumped forward more than 2 seconds past watched progress
                if (current > ytMaxWatched + 2.0) {
                    ytPlayer.seekTo(ytMaxWatched, true);
                    showVideoWarning('Video tidak dapat dipercepat atau dilompati.');
                } else if (current > ytMaxWatched) {
                    ytMaxWatched = current;
                }

                if (duration > 0 && ytMaxWatched >= (duration - 1)) {
                    handleVideoFinished();
                    clearInterval(ytInterval);
                }
            }, 500);
        }

        window.onYouTubeIframeAPIReady = function() {
            var iframe = document.getElementById('lessonVideoIframe');
            if (iframe) {
                ytPlayer = new YT.Player('lessonVideoIframe', {
                    events: {
                        'onReady': function() {
                            startYtMonitor();
                        },
                        'onStateChange': function(event) {
                            if (event.data === 1) { // PLAYING
                                startYtMonitor();
                            } else if (event.data === 0) { // ENDED
                                var duration = ytPlayer.getDuration();
                                if (duration > 0 && ytMaxWatched >= (duration - 2)) {
                                    handleVideoFinished();
                                } else {
                                    ytPlayer.seekTo(ytMaxWatched, true);
                                    showVideoWarning('Harap tonton video pembelajaran secara lengkap.');
                                }
                            }
                        }
                    }
                });
            }
        };

        window.addEventListener('message', function(event) {
            try {
                var data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
                if (data && data.event === 'onStateChange' && data.info === 0) {
                    if (ytPlayer && ytPlayer.getDuration) {
                        var duration = ytPlayer.getDuration();
                        if (duration > 0 && ytMaxWatched >= (duration - 2)) {
                            handleVideoFinished();
                        }
                    } else {
                        handleVideoFinished();
                    }
                }
            } catch(e) {}
        });
    @endif
</script>
@endpush