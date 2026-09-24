<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Check if already enrolled
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Anda sudah terdaftar pada kursus ini.',
                    'enrollment' => $existing,
                ], 422);
            }
            return redirect()->route('learning.course', $course->slug ?? $course->id)
                ->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'started_at' => now(),
            'status' => 'active',
        ]);

        // Calculate total lessons and initialize progress
        $totalLessons = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('is_published', true);
        })->where('is_published', true)->count();

        $courseProgress = CourseProgress::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'completed_lessons' => 0,
                'total_lessons' => $totalLessons,
                'progress_percentage' => 0.0,
                'started_at' => now(),
                'status' => 'not_started',
            ]
        );

        ActivityLogService::log(
            action: 'enrollment_created',
            entity: $enrollment,
            description: "Peserta {$user->name} mendaftar pada kursus: {$course->title}",
            user: $user
        );

        NotificationService::send(
            user: $user,
            title: 'Pendaftaran Kursus Berhasil',
            message: "Selamat belajar di kursus '{$course->title}'!",
            type: 'success',
            data: ['course_id' => $course->id]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pendaftaran berhasil.',
                'enrollment' => $enrollment,
                'progress' => $courseProgress,
            ], 201);
        }

        // Find first lesson to begin learning immediately
        $firstLesson = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('is_published', true);
        })->where('is_published', true)->orderBy('sort_order')->first();

        if ($firstLesson) {
            return redirect()->route('learning.lesson', [$course->id, $firstLesson->id])
                ->with('success', 'Pendaftaran berhasil! Selamat memulai pembelajaran.');
        }

        return redirect()->route('my.courses')->with('success', 'Pendaftaran berhasil!');
    }

    public function myCourses(Request $request)
    {
        $user = Auth::user();

        $enrollments = Enrollment::where('user_id', $user->id)
            ->with(['course' => function ($q) {
                $q->with(['category', 'creator'])
                  ->withCount('lessons');
            }])
            ->latest('enrolled_at')
            ->paginate(9);

        // Attach progress to each enrollment course
        $courseIds = $enrollments->pluck('course_id');
        $progresses = CourseProgress::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        foreach ($enrollments as $enrollment) {
            $enrollment->progress = $progresses->get($enrollment->course_id);
        }

        if ($request->wantsJson()) {
            return response()->json($enrollments);
        }

        return view('user.my_courses', compact('enrollments'));
    }

    public function myCourseDetail($courseId)
    {
        $user = Auth::user();
        $course = Course::with([
            'category',
            'modules.lessons.contents',
            'modules.lessons.lessonProgress' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            },
            'quizzes',
            'certificates' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            },
        ])->findOrFail($courseId);

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        $progress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (request()->wantsJson()) {
            return response()->json([
                'course' => $course,
                'enrollment' => $enrollment,
                'progress' => $progress,
            ]);
        }

        return view('user.my_course_detail', compact('course', 'enrollment', 'progress'));
    }
}
