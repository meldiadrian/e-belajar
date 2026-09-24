<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;

class ProgressService
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Record or update lesson progress (e.g., video watch time or preview).
     */
    public function updateLessonProgress(
        User $user,
        Lesson $lesson,
        int $watchSeconds = 0,
        int $lastPosition = 0,
        float $percentage = 0.0
    ): LessonProgress {
        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'status' => 'in_progress',
                'started_at' => now(),
                'progress_percentage' => 0,
                'watch_seconds' => 0,
                'last_position_seconds' => 0,
            ]
        );

        $isNewStart = ($progress->status === 'not_started');

        $progress->watch_seconds = max($progress->watch_seconds, $watchSeconds);
        $progress->last_position_seconds = $lastPosition;
        $progress->progress_percentage = max($progress->progress_percentage, $percentage);

        if ($progress->status === 'not_started') {
            $progress->status = 'in_progress';
            $progress->started_at = now();
        }

        $progress->save();

        if ($isNewStart) {
            ActivityLogService::log(
                action: 'lesson_started',
                entity: $lesson,
                description: "Peserta {$user->name} mulai mempelajari lesson: {$lesson->title}",
                user: $user
            );
        }

        // Also update course last_accessed_at
        $course = $lesson->module->course;
        CourseProgress::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['last_accessed_at' => now(), 'status' => 'in_progress']
        );

        return $progress;
    }

    /**
     * Mark a lesson as completed and recalculate the course progress.
     */
    public function completeLesson(User $user, Lesson $lesson): LessonProgress
    {
        $progress = LessonProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $alreadyCompleted = ($progress->status === 'completed');

        $progress->started_at = $progress->started_at ?? now();
        $progress->completed_at = now();
        $progress->progress_percentage = 100.0;
        $progress->status = 'completed';
        $progress->save();

        if (!$alreadyCompleted) {
            ActivityLogService::log(
                action: 'lesson_completed',
                entity: $lesson,
                description: "Peserta {$user->name} menyelesaikan lesson: {$lesson->title}",
                user: $user
            );

            NotificationService::send(
                user: $user,
                title: 'Lesson Selesai',
                message: "Materi '{$lesson->title}' telah selesai Anda pelajari.",
                type: 'info'
            );
        }

        $this->recalculateCourseProgress($user, $lesson->module->course);

        return $progress;
    }

    /**
     * Recalculate full course progress for a user.
     */
    public function recalculateCourseProgress(User $user, Course $course): CourseProgress
    {
        // Get all published lessons in published modules
        $publishedLessonIds = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('is_published', true);
        })->where('is_published', true)->pluck('id');

        $totalLessons = $publishedLessonIds->count();

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $publishedLessonIds)
            ->where('status', 'completed')
            ->count();

        // Safe division by zero
        $percentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 0.0;

        $isCompleted = ($totalLessons > 0 && $completedLessons >= $totalLessons);

        $courseProgress = CourseProgress::firstOrNew([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $courseProgress->total_lessons = $totalLessons;
        $courseProgress->completed_lessons = $completedLessons;
        $courseProgress->progress_percentage = $percentage;
        $courseProgress->last_accessed_at = now();

        if ((!$courseProgress->status || $courseProgress->status === 'not_started') && ($completedLessons > 0 || $percentage > 0)) {
            $courseProgress->status = 'in_progress';
            $courseProgress->started_at = $courseProgress->started_at ?? now();
        }

        if ($isCompleted) {
            $courseProgress->status = 'completed';
            $courseProgress->completed_at = $courseProgress->completed_at ?? now();
        }

        $courseProgress->save();

        if ($isCompleted) {
            // Update enrollment status
            Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

            // Issue certificate if enabled and no pending unpassed quizzes
            if ($course->certificate_enabled) {
                $hasPendingQuizzes = false;
                $publishedQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
                if ($publishedQuizIds->isNotEmpty()) {
                    $completedQuizzesCount = \App\Models\QuizAttempt::where('user_id', $user->id)
                        ->whereIn('quiz_id', $publishedQuizIds)
                        ->where(function ($q) {
                            $q->where('status', 'submitted')
                              ->orWhere('passed', true);
                        })
                        ->distinct('quiz_id')
                        ->count('quiz_id');

                    if ($completedQuizzesCount < $publishedQuizIds->count()) {
                        $hasPendingQuizzes = true;
                    }
                }

                if (!$hasPendingQuizzes) {
                    try {
                        $this->certificateService->generateCertificate($user, $course);
                    } catch (\Throwable $e) {
                        // Log error if certificate generation failed
                    }
                }
            }

            ActivityLogService::log(
                action: 'course_completed',
                entity: $course,
                description: "Peserta {$user->name} berhasil menyelesaikan seluruh materi kursus {$course->title} (100%).",
                user: $user
            );

            NotificationService::send(
                user: $user,
                title: 'Kursus Selesai!',
                message: "Selamat! Anda telah menyelesaikan seluruh materi kursus '{$course->title}'.",
                type: 'success'
            );
        }

        return $courseProgress;
    }
}
