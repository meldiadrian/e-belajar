<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function coursePlayer($identifier)
    {
        $course = Course::where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        $user = Auth::user();

        // Check enrollment or admin
        $isEnrolled = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if (!$isEnrolled && !$user->isAdmin()) {
            return redirect()->route('courses.show', $course->slug ?? $course->id)
                ->with('error', 'Silakan mendaftar ke kursus ini terlebih dahulu.');
        }

        // Find last accessed or first incomplete lesson
        $lastProgress = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->latest('updated_at')
            ->first();

        if ($lastProgress) {
            return redirect()->route('learning.lesson', [$course->id, $lastProgress->lesson_id]);
        }

        // First lesson in course
        $firstLesson = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('is_published', true);
        })->where('is_published', true)->orderBy('sort_order')->firstOrFail();

        return redirect()->route('learning.lesson', [$course->id, $firstLesson->id]);
    }

    public function lessonPlayer($courseId, $lessonId)
    {
        $user = Auth::user();
        $course = Course::with([
            'modules' => function ($q) {
                $q->where('is_published', true)->orderBy('sort_order')
                  ->with(['lessons' => function ($lq) {
                      $lq->where('is_published', true)->orderBy('sort_order')
                         ->with('contents');
                  }]);
            },
        ])->findOrFail($courseId);

        $lesson = Lesson::with(['contents', 'quizzes'])->findOrFail($lessonId);

        // Security / Policy check
        if (!$lesson->is_preview && !$user->isAdmin()) {
            $isEnrolled = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'Anda belum terdaftar pada kursus ini.');
            }
        }

        // Track start
        $this->progressService->updateLessonProgress($user, $lesson, 0, 0, 0);

        // Fetch all progress for user in this course
        $lessonProgressMap = LessonProgress::where('user_id', $user->id)
            ->whereHas('lesson.module', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })
            ->get()
            ->keyBy('lesson_id');

        $currentLessonProgress = $lessonProgressMap->get($lesson->id);

        $courseProgress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // Calculate Next & Prev lessons
        $allLessons = collect();
        foreach ($course->modules as $mod) {
            foreach ($mod->lessons as $les) {
                $allLessons->push($les);
            }
        }

        $currentIndex = $allLessons->search(fn($item) => $item->id == $lesson->id);
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = ($currentIndex !== false && $currentIndex < $allLessons->count() - 1)
            ? $allLessons[$currentIndex + 1]
            : null;

        // Check course and lesson quizzes (nilai diabaikan untuk syarat kuis selesai)
        $publishedQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
        $courseQuiz = $lesson->quizzes()->where('is_published', true)->first()
            ?? $course->quizzes()->where('is_published', true)->first();

        $courseQuizPassed = false;
        if ($publishedQuizIds->isNotEmpty()) {
            $completedQuizzesCount = QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $publishedQuizIds)
                ->where(function ($q) {
                    $q->where('status', 'submitted')
                      ->orWhere('passed', true);
                })
                ->distinct('quiz_id')
                ->count('quiz_id');

            $courseQuizPassed = ($completedQuizzesCount >= $publishedQuizIds->count());
        } else {
            $courseQuizPassed = true;
        }

        $lessonQuiz = $lesson->quizzes()->where('is_published', true)->first();
        $lessonQuizPassed = false;
        if ($lessonQuiz) {
            $lessonQuizPassed = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $lessonQuiz->id)
                ->where(function ($q) {
                    $q->where('status', 'submitted')
                      ->orWhere('passed', true);
                })
                ->exists();
        }

        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return view('learning.player', compact(
            'course',
            'lesson',
            'lessonProgressMap',
            'currentLessonProgress',
            'courseProgress',
            'prevLesson',
            'nextLesson',
            'courseQuiz',
            'courseQuizPassed',
            'lessonQuiz',
            'lessonQuizPassed',
            'certificate'
        ));
    }

    public function getCourseProgress($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);

        $progress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        return response()->json([
            'course_id' => $course->id,
            'progress' => $progress,
        ]);
    }

    public function updateProgress(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $user = Auth::user();

        $validated = $request->validate([
            'watch_seconds' => ['nullable', 'integer', 'min:0'],
            'last_position_seconds' => ['nullable', 'integer', 'min:0'],
            'progress_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $progress = $this->progressService->updateLessonProgress(
            $user,
            $lesson,
            $validated['watch_seconds'] ?? 0,
            $validated['last_position_seconds'] ?? 0,
            (float) ($validated['progress_percentage'] ?? 0)
        );

        return response()->json([
            'message' => 'Progres lesson diperbarui.',
            'progress' => $progress,
        ]);
    }

    public function completeLesson(Request $request, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $user = Auth::user();

        $progress = $this->progressService->completeLesson($user, $lesson);
        $course = $lesson->module->course;
        $courseProgress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        // 1. Check if the current lesson has a quiz that has not been submitted
        $targetQuiz = null;
        $lessonQuiz = $lesson->quizzes()->where('is_published', true)->first();
        if ($lessonQuiz) {
            $submitted = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $lessonQuiz->id)
                ->where(function ($q) {
                    $q->where('status', 'submitted')
                      ->orWhere('passed', true);
                })
                ->exists();
            if (!$submitted) {
                $targetQuiz = $lessonQuiz;
            }
        }

        // 2. If no lesson-specific unsubmitted quiz, check if all course lessons are completed (100%) and course has unsubmitted quiz
        if (!$targetQuiz && $courseProgress && (float) $courseProgress->progress_percentage >= 100.0) {
            $courseQuizzes = $course->quizzes()->where('is_published', true)->get();
            foreach ($courseQuizzes as $cQuiz) {
                $submitted = QuizAttempt::where('user_id', $user->id)
                    ->where('quiz_id', $cQuiz->id)
                    ->where(function ($q) {
                        $q->where('status', 'submitted')
                          ->orWhere('passed', true);
                    })
                    ->exists();
                if (!$submitted) {
                    $targetQuiz = $cQuiz;
                    break;
                }
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $targetQuiz ? 'Materi selesai. Menampilkan kuis evaluasi.' : 'Lesson ditandai selesai.',
                'lesson_progress' => $progress,
                'course_progress' => $courseProgress,
                'quiz_url' => $targetQuiz ? route('quiz.show', $targetQuiz->id) : null,
            ]);
        }

        if ($targetQuiz) {
            return redirect()->route('quiz.show', $targetQuiz->id)
                ->with('info', 'Materi berhasil diselesaikan! Silakan kerjakan kuis evaluasi berikut untuk menguji pemahaman Anda.');
        }

        return back()->with('success', 'Materi berhasil diselesaikan!');
    }
}
