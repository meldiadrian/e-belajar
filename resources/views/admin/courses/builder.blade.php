@extends('layouts.admin')

@section('title', 'Course Builder - ' . $course->title)
@section('page_title', 'Course Builder')

@section('content')
    <div class="space-y-8">
        <!-- Course Summary Header & Status Toggle -->
        <div
            class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        Status: {{ $course->status }}
                    </span>
                    <span class="text-xs text-slate-400">&bull; {{ $course->category->name ?? '-' }}</span>
                </div>
                <h1 class="text-xl font-black text-slate-900 leading-tight">{{ $course->title }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $course->modules->count() }} Modul &bull;
                    {{ $course->modules->sum(fn($m) => $m->lessons->count()) }} Pelajaran</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('courses.show', $course->slug ?? $course->id) }}" target="_blank"
                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Pratinjau Halaman &rarr;
                </a>

                <form action="{{ route('admin.courses.toggle-publish', $course->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white {{ $course->status === 'published' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-700 hover:bg-emerald-800' }} shadow-xs transition-colors">
                        {{ $course->status === 'published' ? 'Batalkan Publikasi (Draft)' : 'Publikasikan Sekarang' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Builder Workflow Steps -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: Modules & Lessons Hierarchy (Curriculum Tree) -->
            <div class="lg:col-span-8 space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-base font-black text-slate-900">Struktur Kurikulum Pembelajaran</h3>
                    <button onclick="document.getElementById('modalAddModule').classList.remove('hidden')"
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-xs">
                        + Tambah Modul Baru
                    </button>
                </div>

                <!-- Modules Accordion -->
                <div class="space-y-6">
                    @forelse($course->modules as $mIdx => $module)
                        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                            <!-- Module Header Bar -->
                            <div
                                class="bg-slate-50 p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                        {{ $mIdx + 1 }}
                                    </span>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900">{{ $module->title }}</h4>
                                        <p class="text-[11px] text-slate-500">{{ $module->lessons->count() }} Pelajaran</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button onclick="openAddLessonModal({{ $module->id }}, '{{ addslashes($module->title) }}')"
                                        class="px-3 py-1.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 rounded-lg text-xs font-bold border border-emerald-200">
                                        + Tambah Lesson
                                    </button>
                                    <form action="{{ route('admin.modules.destroy', $module->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus modul beserta seluruh lesson di dalamnya?')"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Lessons inside this Module -->
                            <div class="divide-y divide-slate-100">
                                @forelse($module->lessons as $lIdx => $lesson)
                                    <div
                                        class="p-4 sm:p-5 hover:bg-slate-50/50 transition-colors flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <span
                                                class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-bold">
                                                {{ $lIdx + 1 }}
                                            </span>
                                            <div class="truncate">
                                                <div class="font-bold text-xs text-slate-900 truncate">{{ $lesson->title }}</div>
                                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                                    <span
                                                        class="uppercase font-semibold text-emerald-700">{{ $lesson->lesson_type }}</span>
                                                    <span>&bull;</span>
                                                    <span>{{ $lesson->duration > 0 ? round($lesson->duration / 60) . ' menit' : 'Fleksibel' }}</span>
                                                    @if($lesson->is_preview)
                                                        <span
                                                            class="px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded-md font-bold">Pratinjau</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            <button type="button"
                                                onclick="openEditLessonModal({{ json_encode([
                                                    'id' => $lesson->id,
                                                    'title' => $lesson->title,
                                                    'lesson_type' => $lesson->lesson_type,
                                                    'duration' => $lesson->duration,
                                                    'is_preview' => (bool)$lesson->is_preview,
                                                    'content' => $lesson->content,
                                                    'video_url' => $lesson->contents->firstWhere('type', 'video')?->url ?? '',
                                                ]) }})"
                                                class="p-1 text-slate-400 hover:text-emerald-700 rounded-lg hover:bg-slate-100 transition-colors"
                                                title="Edit Lesson">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST"
                                                onsubmit="return confirm('Hapus lesson ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-colors" title="Hapus Lesson">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-xs text-slate-400">
                                        Belum ada lesson pada modul ini. Klik tombol <span class="font-semibold text-emerald-700">+
                                            Tambah Lesson</span> di atas.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
                            <div
                                class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800">Kurikulum Masih Kosong</h4>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Tambahkan modul pertama untuk mulai mengunggah materi
                                pelajaran.</p>
                            <button onclick="document.getElementById('modalAddModule').classList.remove('hidden')"
                                class="px-5 py-2.5 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800">
                                + Tambah Modul Pertama
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Quizzes & Assessments Builder -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs space-y-3">
    <div class="flex justify-between items-center">
        <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider">
            Asesmen Kuis Pembelajaran
        </h3>

        <button onclick="document.getElementById('modalAddQuiz').classList.remove('hidden')"
            class="px-2 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg text-[11px] font-bold shadow-xs">
            + Buat Kuis
        </button>
    </div>


                    <div class="space-y-4">
                        @forelse($course->quizzes as $quiz)
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="truncate mr-1">
                                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $quiz->title }}">
                                            {{ $quiz->title }}</h4>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">Passing Score:
                                            {{ $quiz->passing_score }}% &bull; {{ $quiz->questions->count() }} Soal</span>
                                    </div>
                                    <div class="flex items-center gap-1 shrink-0">
                                        <button type="button"
                                            onclick="openEditQuizModal({{ $quiz->id }}, '{{ addslashes($quiz->title) }}', {{ $quiz->passing_score }}, {{ $quiz->time_limit }}, {{ $quiz->max_attempts }}, {{ $quiz->shuffle_questions ? 'true' : 'false' }}, {{ $quiz->shuffle_options ? 'true' : 'false' }})"
                                            class="p-1.5 text-slate-400 hover:text-amber-600 rounded-lg hover:bg-white transition-colors"
                                            title="Edit Kuis">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus kuis ini beserta seluruh soalnya?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-white transition-colors"
                                                title="Hapus Kuis">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                        <button
                                            onclick="openAddQuestionModal({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')"
                                            class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded-md text-[10px] font-bold">
                                            + Soal
                                        </button>
                                    </div>
                                </div>

                                <!-- Questions under Quiz -->
                                <div class="space-y-1.5">
                                    @foreach($quiz->questions as $qIdx => $q)
                                        <div
                                            class="p-2 rounded-lg bg-white border border-slate-100 flex items-center justify-between text-[11px]">
                                            <span class="truncate mr-2 font-medium">{{ $qIdx + 1 }}.
                                                {{ Str::limit($q->question, 30) }}</span>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <span class="text-[9px] text-slate-400 font-bold">{{ $q->points }} P</span>
                                                <button type="button"
                                                    onclick="openEditQuestionModal({{ json_encode([
                                                        'id' => $q->id,
                                                        'quiz_id' => $quiz->id,
                                                        'quiz_title' => $quiz->title,
                                                        'question' => $q->question,
                                                        'type' => $q->type,
                                                        'points' => $q->points,
                                                        'explanation' => $q->explanation,
                                                        'options' => $q->options->map(fn($opt) => [
                                                            'id' => $opt->id,
                                                            'text' => $opt->option_text,
                                                            'is_correct' => (bool)$opt->is_correct
                                                        ])->values()->all()
                                                    ]) }})"
                                                    class="text-slate-400 hover:text-amber-600 p-0.5 rounded hover:bg-slate-50 transition-colors"
                                                    title="Edit Soal">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('admin.questions.destroy', $q->id) }}" method="POST"
                                                    class="inline" onsubmit="return confirm('Hapus soal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-500 hover:text-red-700 font-bold leading-none" title="Hapus Soal">&times;</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-400">Belum ada evaluasi kuis.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 1: Add Module -->
    <div id="modalAddModule"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Modul Pembelajaran</h3>
            <form action="{{ route('admin.modules.store', $course->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Modul *</label>
                    <input type="text" name="title" required placeholder="Contoh: Modul 1 - Pengenalan Dasar"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modalAddModule').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800">Simpan
                        Modul</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Add Lesson -->
    <div id="modalAddLesson"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900">Tambah Lesson Baru</h3>
            <p id="lessonModuleTitle" class="text-xs text-emerald-700 font-semibold"></p>
            <form id="formAddLesson" action="" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pelajaran / Materi *</label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Materi *</label>
                        <select name="lesson_type" id="lessonTypeSelect" onchange="toggleLessonTypeFields(this.value)"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                            <option value="text">Teks / Artikel</option>
                            <option value="video">Video Pembelajaran</option>
                            <option value="document">Dokumen / PDF</option>
                            <option value="quiz">Kuis Asesmen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Durasi (Detik)</label>
                        <input type="number" name="duration" value="300"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <div id="fieldVideoUrl" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tautan URL Video (YouTube / MP4)</label>
                    <input type="text" name="video_url" placeholder="https://www.youtube.com/watch?v=..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>

                <div id="fieldDocumentFile" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Dokumen (PDF, PPT, DOC, maks
                        50MB)</label>
                    <input type="file" name="document_file" class="w-full text-xs text-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konten Teks Pembelajaran</label>
                    <textarea name="content" rows="4" placeholder="Uraian isi materi..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="is_preview" value="1" class="rounded text-emerald-600">
                        <span class="text-slate-700">Izinkan Pratinjau Gratis (Bisa dilihat sebelum enroll)</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAddLesson').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800">Simpan
                        Lesson</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2B: Edit Lesson -->
    <div id="modalEditLesson"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-base font-bold text-slate-900">Edit Materi / Lesson</h3>
            <form id="formEditLesson" action="" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pelajaran / Materi *</label>
                    <input type="text" name="title" id="editLessonTitle" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Materi *</label>
                        <select name="lesson_type" id="editLessonType" onchange="toggleEditLessonTypeFields(this.value)"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                            <option value="text">Teks / Artikel</option>
                            <option value="video">Video Pembelajaran</option>
                            <option value="document">Dokumen / PDF</option>
                            <option value="quiz">Kuis Asesmen</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Durasi (Detik)</label>
                        <input type="number" name="duration" id="editLessonDuration" value="300"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <div id="fieldEditVideoUrl" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tautan URL Video (YouTube / MP4)</label>
                    <input type="text" name="video_url" id="editLessonVideoUrl" placeholder="https://www.youtube.com/watch?v=..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>

                <div id="fieldEditDocumentFile" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Dokumen Baru (Opsional, PDF, PPT, DOC, maks 50MB)</label>
                    <input type="file" name="document_file" class="w-full text-xs text-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konten Teks Pembelajaran</label>
                    <textarea name="content" id="editLessonContent" rows="4" placeholder="Uraian isi materi..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="is_preview" id="editLessonIsPreview" value="1" class="rounded text-emerald-600">
                        <span class="text-slate-700">Izinkan Pratinjau Gratis (Bisa dilihat sebelum enroll)</span>
                    </label>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEditLesson').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Add Quiz -->
    <div id="modalAddQuiz"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900">Buat Asesmen Kuis Baru</h3>
            <form action="{{ route('admin.quizzes.store', $course->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kuis *</label>
                    <input type="text" name="title" required placeholder="Contoh: Kuis Evaluasi Pemahaman Modul 1"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Passing Score (%) *</label>
                        <input type="number" name="passing_score" value="70" min="1" max="100" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Waktu (Menit)</label>
                        <input type="number" name="time_limit" value="15" min="0"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Maksimal Percobaan (0 = Tanpa batas)</label>
                    <input type="number" name="max_attempts" value="3" min="0"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="shuffle_questions" value="1" class="rounded text-emerald-600">
                        <span class="text-slate-700">Acak Urutan Soal (Shuffle)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="shuffle_options" value="1" class="rounded text-emerald-600">
                        <span class="text-slate-700">Acak Urutan Opsi Pilihan</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAddQuiz').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-500 text-slate-950 font-bold hover:bg-amber-400">Simpan
                        Kuis</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3B: Edit Quiz -->
    <div id="modalEditQuiz"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
            <h3 class="text-base font-bold text-slate-900">Edit Asesmen Kuis</h3>
            <form id="formEditQuiz" action="" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Kuis *</label>
                    <input type="text" name="title" id="editQuizTitle" required
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Passing Score (%) *</label>
                        <input type="number" name="passing_score" id="editQuizPassingScore" min="1" max="100" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Waktu (Menit)</label>
                        <input type="number" name="time_limit" id="editQuizTimeLimit" min="0"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Maksimal Percobaan (0 = Tanpa batas)</label>
                    <input type="number" name="max_attempts" id="editQuizMaxAttempts" min="0"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="shuffle_questions" id="editQuizShuffleQuestions" value="1"
                            class="rounded text-emerald-600">
                        <span class="text-slate-700">Acak Urutan Soal (Shuffle)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs">
                        <input type="checkbox" name="shuffle_options" id="editQuizShuffleOptions" value="1"
                            class="rounded text-emerald-600">
                        <span class="text-slate-700">Acak Urutan Opsi Pilihan</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEditQuiz').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-500 text-slate-950 font-bold hover:bg-amber-400">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4: Add Question -->
    <div id="modalAddQuestion"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-base font-bold text-slate-900">Tambah Butir Pertanyaan</h3>
            <p id="questionQuizTitle" class="text-xs text-amber-700 font-semibold"></p>
            <form id="formAddQuestion" action="" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan / Soal *</label>
                    <textarea name="question" rows="3" required placeholder="Tuliskan butir soal di sini..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Soal *</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                            <option value="single_choice">Pilihan Ganda (Single Choice)</option>
                            <option value="true_false">Benar / Salah (True/False)</option>
                            <option value="multiple_choice">Pilihan Jamak (Multiple Choice)</option>
                            <option value="short_answer">Isian Singkat</option>
                            <option value="essay">Uraian / Essay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot Nilai (Poin) *</label>
                        <input type="number" name="points" value="10" min="1" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <!-- Options inputs -->
                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700">Pilihan Jawaban & Jawaban Benar (Radio menandai
                        benar):</label>
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600">
                            <input type="text" name="options[{{ $i }}][text]" placeholder="Opsi {{ chr(65 + $i) }}"
                                class="flex-1 px-3 py-1.5 rounded-lg border border-slate-300 text-xs">
                        </div>
                    @endfor
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Penjelasan Kunci Jawaban (Muncul setelah
                        submit)</label>
                    <textarea name="explanation" rows="2"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalAddQuestion').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800">Simpan
                        Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 4B: Edit Question -->
    <div id="modalEditQuestion"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-base font-bold text-slate-900">Edit Butir Pertanyaan</h3>
            <p id="editQuestionQuizTitle" class="text-xs text-amber-700 font-semibold"></p>
            <form id="formEditQuestion" action="" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan / Soal *</label>
                    <textarea name="question" id="editQuestionText" rows="3" required placeholder="Tuliskan butir soal di sini..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Soal *</label>
                        <select name="type" id="editQuestionType" onchange="toggleEditQuestionOptions(this.value)"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                            <option value="single_choice">Pilihan Ganda (Single Choice)</option>
                            <option value="true_false">Benar / Salah (True/False)</option>
                            <option value="multiple_choice">Pilihan Jamak (Multiple Choice)</option>
                            <option value="short_answer">Isian Singkat</option>
                            <option value="essay">Uraian / Essay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot Nilai (Poin) *</label>
                        <input type="number" name="points" id="editQuestionPoints" value="10" min="1" required
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    </div>
                </div>

                <!-- Options inputs -->
                <div id="editQuestionOptionsContainer" class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700">Pilihan Jawaban & Jawaban Benar (Radio menandai benar):</label>
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-2">
                            <input type="radio" name="correct_option" id="editCorrectOption{{ $i }}" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600">
                            <input type="text" name="options[{{ $i }}][text]" id="editOptionText{{ $i }}" placeholder="Opsi {{ chr(65 + $i) }}"
                                class="flex-1 px-3 py-1.5 rounded-lg border border-slate-300 text-xs">
                        </div>
                    @endfor
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Penjelasan Kunci Jawaban (Muncul setelah submit)</label>
                    <textarea name="explanation" id="editQuestionExplanation" rows="2"
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalEditQuestion').classList.add('hidden')"
                        class="px-4 py-2 text-xs font-bold text-slate-500">Batal</button>
                    <button type="submit"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-amber-500 text-slate-950 font-bold hover:bg-amber-400">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddLessonModal(moduleId, moduleTitle) {
            document.getElementById('lessonModuleTitle').innerText = 'Modul: ' + moduleTitle;
            document.getElementById('formAddLesson').action = '/admin/modules/' + moduleId + '/lessons';
            document.getElementById('modalAddLesson').classList.remove('hidden');
        }

        function toggleLessonTypeFields(type) {
            document.getElementById('fieldVideoUrl').classList.toggle('hidden', type !== 'video');
            document.getElementById('fieldDocumentFile').classList.toggle('hidden', type !== 'document');
        }

        function openEditLessonModal(lesson) {
            document.getElementById('formEditLesson').action = '/admin/lessons/' + lesson.id;
            document.getElementById('editLessonTitle').value = lesson.title || '';
            document.getElementById('editLessonType').value = lesson.lesson_type || 'video';
            document.getElementById('editLessonDuration').value = lesson.duration || 0;
            document.getElementById('editLessonVideoUrl').value = lesson.video_url || '';
            document.getElementById('editLessonContent').value = lesson.content || '';
            document.getElementById('editLessonIsPreview').checked = Boolean(lesson.is_preview);

            toggleEditLessonTypeFields(lesson.lesson_type || 'video');
            document.getElementById('modalEditLesson').classList.remove('hidden');
        }

        function toggleEditLessonTypeFields(type) {
            document.getElementById('fieldEditVideoUrl').classList.toggle('hidden', type !== 'video');
            document.getElementById('fieldEditDocumentFile').classList.toggle('hidden', type !== 'document');
        }

        function openAddQuestionModal(quizId, quizTitle) {
            document.getElementById('questionQuizTitle').innerText = 'Kuis: ' + quizTitle;
            document.getElementById('formAddQuestion').action = '/admin/quizzes/' + quizId + '/questions';
            document.getElementById('modalAddQuestion').classList.remove('hidden');
        }

        function openEditQuestionModal(q) {
            document.getElementById('editQuestionQuizTitle').innerText = 'Kuis: ' + (q.quiz_title || '');
            document.getElementById('formEditQuestion').action = '/admin/questions/' + q.id;
            document.getElementById('editQuestionText').value = q.question || '';
            document.getElementById('editQuestionType').value = q.type || 'single_choice';
            document.getElementById('editQuestionPoints').value = q.points || 10;
            document.getElementById('editQuestionExplanation').value = q.explanation || '';

            let hasCorrect = false;
            for (let i = 0; i < 4; i++) {
                const textInput = document.getElementById('editOptionText' + i);
                const radioInput = document.getElementById('editCorrectOption' + i);
                if (textInput && radioInput) {
                    if (q.options && q.options[i]) {
                        textInput.value = q.options[i].text || '';
                        radioInput.checked = Boolean(q.options[i].is_correct);
                        if (q.options[i].is_correct) hasCorrect = true;
                    } else {
                        textInput.value = '';
                        radioInput.checked = false;
                    }
                }
            }
            if (!hasCorrect) {
                const firstRadio = document.getElementById('editCorrectOption0');
                if (firstRadio) firstRadio.checked = true;
            }

            toggleEditQuestionOptions(q.type || 'single_choice');
            document.getElementById('modalEditQuestion').classList.remove('hidden');
        }

        function toggleEditQuestionOptions(type) {
            const isChoice = ['single_choice', 'true_false', 'multiple_choice'].includes(type);
            const container = document.getElementById('editQuestionOptionsContainer');
            if (container) {
                container.classList.toggle('hidden', !isChoice);
            }
        }

        function openEditQuizModal(quizId, title, passingScore, timeLimit, maxAttempts, shuffleQ, shuffleO) {
            document.getElementById('formEditQuiz').action = '/admin/quizzes/' + quizId;
            document.getElementById('editQuizTitle').value = title;
            document.getElementById('editQuizPassingScore').value = passingScore;
            document.getElementById('editQuizTimeLimit').value = timeLimit;
            document.getElementById('editQuizMaxAttempts').value = maxAttempts;
            document.getElementById('editQuizShuffleQuestions').checked = Boolean(shuffleQ);
            document.getElementById('editQuizShuffleOptions').checked = Boolean(shuffleO);
            document.getElementById('modalEditQuiz').classList.remove('hidden');
        }
    </script>
@endsection