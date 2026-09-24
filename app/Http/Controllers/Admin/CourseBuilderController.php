<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Tag;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseBuilderController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'creator'])
            ->withCount(['modules', 'lessons', 'enrollments'])
            ->latest()
            ->paginate(12);

        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('admin.courses.index', compact('courses', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('admin.courses.create', compact('categories', 'tags'));
    }

    public function builder($courseId)
    {
        $course = Course::with([
            'category',
            'tags',
            'modules' => function ($q) {
                $q->orderBy('sort_order')->with([
                    'lessons' => function ($lq) {
                        $lq->orderBy('sort_order')->with([
                            'contents' => fn($cq) => $cq->orderBy('sort_order'),
                            'quizzes.questions.options',
                        ]);
                    },
                ]);
            },
            'quizzes' => function ($q) {
                $q->with('questions.options');
            },
        ])->findOrFail($courseId);

        $categories = Category::where('is_active', true)->get();
        $tags = Tag::all();

        return view('admin.courses.builder', compact('course', 'categories', 'tags'));
    }

    public function togglePublish($courseId)
    {
        $course = Course::findOrFail($courseId);
        $newStatus = ($course->status === 'published') ? 'draft' : 'published';

        $course->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? ($course->published_at ?? now()) : $course->published_at,
        ]);

        ActivityLogService::log(
            action: $newStatus === 'published' ? 'course_published' : 'course_unpublished',
            entity: $course,
            description: "Status kursus '{$course->title}' diubah menjadi {$newStatus}."
        );

        return back()->with('success', "Status kursus berhasil diubah menjadi {$newStatus}.");
    }

    // API / Getter endpoints for modules & lessons
    public function getModules($courseId)
    {
        $course = Course::findOrFail($courseId);
        $modules = $course->modules()->with('lessons')->get();
        return response()->json($modules);
    }

    public function getLessons($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $lessons = $module->lessons()->with('contents')->get();
        return response()->json($lessons);
    }

    public function getLesson($id)
    {
        $lesson = Lesson::with(['contents', 'module.course'])->findOrFail($id);
        return response()->json($lesson);
    }

    // Module management
    public function storeModule(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $maxOrder = CourseModule::where('course_id', $course->id)->max('sort_order') ?? 0;

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $maxOrder + 1,
            'is_published' => true,
        ]);

        ActivityLogService::log(
            action: 'module_created',
            entity: $module,
            description: "Membuat modul baru '{$module->title}' pada kursus '{$course->title}'."
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Modul berhasil ditambahkan.', 'module' => $module], 201);
        }

        return back()->with('success', 'Modul berhasil ditambahkan.');
    }

    public function updateModule(Request $request, $moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $oldValues = $module->toArray();
        $module->update($validated);

        ActivityLogService::log(
            action: 'module_updated',
            entity: $module,
            description: "Memperbarui modul '{$module->title}'.",
            oldValues: $oldValues,
            newValues: $module->fresh()->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Modul berhasil diperbarui.', 'module' => $module]);
        }

        return back()->with('success', 'Modul berhasil diperbarui.');
    }

    public function deleteModule($moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);
        $title = $module->title;
        $oldValues = $module->toArray();
        $module->delete();

        ActivityLogService::log(
            action: 'module_deleted',
            entity: null,
            description: "Menghapus modul '{$title}'.",
            oldValues: $oldValues
        );

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Modul berhasil dihapus.']);
        }

        return back()->with('success', 'Modul berhasil dihapus.');
    }

    // Lesson management
    public function storeLesson(Request $request, $moduleId)
    {
        $module = CourseModule::findOrFail($moduleId);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', 'in:video,document,text,quiz'],
            'content' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['boolean'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip', 'max:51200'],
        ]);

        $slug = Str::slug($validated['title']);
        $maxOrder = Lesson::where('course_module_id', $module->id)->max('sort_order') ?? 0;

        $lesson = Lesson::create([
            'course_module_id' => $module->id,
            'title' => $validated['title'],
            'slug' => $slug . '-' . uniqid(),
            'lesson_type' => $validated['lesson_type'],
            'content' => $validated['content'] ?? null,
            'duration' => $validated['duration'] ?? 0,
            'sort_order' => $maxOrder + 1,
            'is_preview' => $request->boolean('is_preview'),
            'is_published' => true,
        ]);

        // Process attachments / contents
        if ($validated['lesson_type'] === 'video' && !empty($validated['video_url'])) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'video',
                'title' => 'Video Pembelajaran: ' . $lesson->title,
                'url' => $validated['video_url'],
                'sort_order' => 1,
            ]);
        } elseif ($validated['lesson_type'] === 'document' && $request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $path = $file->store('documents', 'public');

            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'document',
                'title' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'sort_order' => 1,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Lesson berhasil ditambahkan.', 'lesson' => $lesson], 201);
        }

        ActivityLogService::log(
            action: 'lesson_created',
            entity: $lesson,
            description: "Membuat materi/pelajaran baru '{$lesson->title}' pada modul '{$module->title}'."
        );

        return back()->with('success', 'Lesson berhasil ditambahkan.');
    }

    public function updateLesson(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'lesson_type' => ['sometimes', 'in:video,document,text,quiz'],
            'content' => ['nullable', 'string'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_preview' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,zip', 'max:51200'],
        ]);

        $oldValues = $lesson->toArray();
        $lesson->update([
            'title' => $validated['title'] ?? $lesson->title,
            'lesson_type' => $validated['lesson_type'] ?? $lesson->lesson_type,
            'content' => $validated['content'] ?? $lesson->content,
            'duration' => isset($validated['duration']) ? (int) $validated['duration'] : $lesson->duration,
            'is_preview' => $request->has('is_preview') ? $request->boolean('is_preview') : $lesson->is_preview,
            'is_published' => $request->has('is_published') ? $request->boolean('is_published') : $lesson->is_published,
            'sort_order' => $validated['sort_order'] ?? $lesson->sort_order,
        ]);

        // Process attachments / contents
        if ($lesson->lesson_type === 'video' && $request->has('video_url')) {
            $videoContent = $lesson->contents()->where('type', 'video')->first();
            if ($videoContent) {
                $videoContent->update(['url' => $request->input('video_url')]);
            } elseif (!empty($request->input('video_url'))) {
                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'type' => 'video',
                    'title' => 'Video Pembelajaran: ' . $lesson->title,
                    'url' => $request->input('video_url'),
                    'sort_order' => 1,
                ]);
            }
        } elseif ($lesson->lesson_type === 'document' && $request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $path = $file->store('documents', 'public');
            $docContent = $lesson->contents()->where('type', 'document')->first();
            if ($docContent) {
                $docContent->update([
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            } else {
                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'type' => 'document',
                    'title' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'sort_order' => 1,
                ]);
            }
        }

        ActivityLogService::log(
            action: 'lesson_updated',
            entity: $lesson,
            description: "Memperbarui materi/pelajaran '{$lesson->title}'.",
            oldValues: $oldValues,
            newValues: $lesson->fresh()->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Lesson berhasil diperbarui.', 'lesson' => $lesson]);
        }

        return back()->with('success', 'Lesson berhasil diperbarui.');
    }

    public function deleteLesson($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $title = $lesson->title;
        $oldValues = $lesson->toArray();
        $lesson->delete();

        ActivityLogService::log(
            action: 'lesson_deleted',
            entity: null,
            description: "Menghapus materi/pelajaran '{$title}'.",
            oldValues: $oldValues
        );

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Lesson berhasil dihapus.']);
        }

        return back()->with('success', 'Lesson berhasil dihapus.');
    }

    // Quiz builder
    public function storeQuiz(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['nullable', 'integer', 'min:0'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'shuffle_questions' => ['boolean'],
            'shuffle_options' => ['boolean'],
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit' => $validated['time_limit'] ?? 0,
            'passing_score' => $validated['passing_score'],
            'max_attempts' => $validated['max_attempts'] ?? 0,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'is_published' => true,
        ]);

        ActivityLogService::log(
            action: 'quiz_created',
            entity: $quiz,
            description: "Membuat kuis baru '{$quiz->title}' pada kursus '{$course->title}'."
        );

        return back()->with('success', 'Kuis berhasil dibuat. Silakan tambahkan pertanyaan.');
    }

    public function updateQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validated = $request->validate([
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['nullable', 'integer', 'min:0'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'shuffle_questions' => ['boolean'],
            'shuffle_options' => ['boolean'],
        ]);

        $oldValues = $quiz->toArray();

        $quiz->update([
            'lesson_id' => $validated['lesson_id'] ?? $quiz->lesson_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit' => $validated['time_limit'] ?? 0,
            'passing_score' => $validated['passing_score'],
            'max_attempts' => $validated['max_attempts'] ?? 0,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
        ]);

        ActivityLogService::log(
            action: 'quiz_updated',
            entity: $quiz,
            description: "Memperbarui kuis '{$quiz->title}'.",
            oldValues: $oldValues,
            newValues: $quiz->fresh()->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Kuis berhasil diperbarui.', 'quiz' => $quiz]);
        }

        return back()->with('success', 'Kuis berhasil diperbarui.');
    }

    public function deleteQuiz($quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $title = $quiz->title;
        $oldValues = $quiz->toArray();
        $quiz->delete();

        ActivityLogService::log(
            action: 'quiz_deleted',
            entity: null,
            description: "Menghapus kuis '{$title}'.",
            oldValues: $oldValues
        );

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Kuis berhasil dihapus.']);
        }

        return back()->with('success', 'Kuis berhasil dihapus.');
    }

    public function storeQuestion(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'type' => ['required', 'in:single_choice,multiple_choice,true_false,short_answer,essay'],
            'points' => ['required', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'options.*.text' => ['required_with:options', 'string'],
            'correct_option' => ['nullable'], // index or id of correct option
        ]);

        $maxOrder = Question::where('quiz_id', $quiz->id)->max('sort_order') ?? 0;

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question' => $validated['question'],
            'type' => $validated['type'],
            'points' => $validated['points'],
            'sort_order' => $maxOrder + 1,
            'explanation' => $validated['explanation'] ?? null,
        ]);

        if (!empty($validated['options'])) {
            foreach ($validated['options'] as $idx => $optData) {
                $isCorrect = false;
                if ($question->type === 'single_choice' || $question->type === 'true_false') {
                    $isCorrect = (string) $idx === (string) ($request->correct_option ?? '');
                } elseif ($question->type === 'multiple_choice') {
                    $isCorrect = isset($optData['is_correct']) && $optData['is_correct'];
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optData['text'],
                    'is_correct' => $isCorrect,
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        ActivityLogService::log(
            action: 'question_created',
            entity: $question,
            description: "Menambahkan butir pertanyaan baru ke kuis '{$quiz->title}'."
        );

        return back()->with('success', 'Pertanyaan berhasil ditambahkan ke kuis.');
    }

    public function updateQuestion(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $validated = $request->validate([
            'question' => ['required', 'string'],
            'type' => ['required', 'in:single_choice,multiple_choice,true_false,short_answer,essay'],
            'points' => ['required', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
            'options' => ['nullable', 'array'],
            'options.*.text' => ['nullable', 'string'],
            'correct_option' => ['nullable'],
        ]);

        $oldValues = $question->toArray();

        $question->update([
            'question' => $validated['question'],
            'type' => $validated['type'],
            'points' => $validated['points'],
            'explanation' => $validated['explanation'] ?? null,
        ]);

        if (in_array($question->type, ['single_choice', 'multiple_choice', 'true_false'])) {
            $question->options()->delete();
            if (!empty($validated['options'])) {
                $sortOrder = 1;
                foreach ($validated['options'] as $idx => $optData) {
                    $text = trim($optData['text'] ?? '');
                    if ($text === '') {
                        continue;
                    }

                    $isCorrect = false;
                    if ($question->type === 'single_choice' || $question->type === 'true_false') {
                        $isCorrect = (string) $idx === (string) ($request->correct_option ?? '');
                    } elseif ($question->type === 'multiple_choice') {
                        $isCorrect = !empty($optData['is_correct']);
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $text,
                        'is_correct' => $isCorrect,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        }

        ActivityLogService::log(
            action: 'question_updated',
            entity: $question,
            description: "Memperbarui butir pertanyaan pada kuis '" . ($question->quiz->title ?? 'Kuis') . "'.",
            oldValues: $oldValues,
            newValues: $question->fresh()->toArray()
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pertanyaan berhasil diperbarui.', 'question' => $question->load('options')]);
        }

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        $quizTitle = $question->quiz->title ?? 'Kuis';
        $oldValues = $question->toArray();
        $question->delete();

        ActivityLogService::log(
            action: 'question_deleted',
            entity: null,
            description: "Menghapus butir pertanyaan dari '{$quizTitle}'.",
            oldValues: $oldValues
        );

        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
