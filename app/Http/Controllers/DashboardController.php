<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Notification;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard();
    }

    protected function userDashboard()
    {
        $user = Auth::user();

        // Enrolled courses with progress
        $enrollments = Enrollment::where('user_id', $user->id)
            ->with([
                'course' => function ($q) {
                    $q->with('category')->withCount('lessons');
                }
            ])
            ->latest('enrolled_at')
            ->get();

        $courseIds = $enrollments->pluck('course_id');
        $progresses = CourseProgress::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        foreach ($enrollments as $enr) {
            $enr->progress = $progresses->get($enr->course_id);
        }

        // Last accessed lesson
        $lastLessonProgress = LessonProgress::where('user_id', $user->id)
            ->with(['lesson.module.course'])
            ->latest('updated_at')
            ->first();

        // Last quiz attempt
        $lastQuizAttempt = QuizAttempt::where('user_id', $user->id)
            ->with(['quiz.course'])
            ->latest('created_at')
            ->first();

        // Certificates
        $certificates = Certificate::where('user_id', $user->id)
            ->with('course')
            ->latest('issued_at')
            ->take(5)
            ->get();

        // Learning history
        $learningHistory = LessonProgress::where('user_id', $user->id)
            ->with('lesson.module.course')
            ->latest('updated_at')
            ->take(8)
            ->get();

        // Notifications
        $notifications = Notification::where('user_id', $user->id)
            ->latest('created_at')
            ->take(5)
            ->get();

        // Summary stats
        $stats = [
            'total_enrolled' => $enrollments->count(),
            'in_progress' => $progresses->where('status', 'in_progress')->count(),
            'completed_courses' => $progresses->where('status', 'completed')->count(),
            'total_certificates' => Certificate::where('user_id', $user->id)->count(),
        ];

        // Catalog Courses for user dashboard
        $coursesQuery = Course::where('status', 'published')
            ->with(['category', 'creator', 'tags'])
            ->withCount(['lessons', 'enrollments']);

        if (request()->filled('search')) {
            $search = request('search');
            $coursesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (request()->filled('category')) {
            $coursesQuery->whereHas('category', function ($q) {
                $q->where('slug', request('category'))
                    ->orWhere('id', request('category'));
            });
        }

        $sort = request()->get('sort', 'newest');
        if ($sort === 'popular') {
            $coursesQuery->orderByDesc('enrollments_count');
        } elseif ($sort === 'title') {
            $coursesQuery->orderBy('title', 'asc');
        } else {
            $coursesQuery->latest();
        }

        $courses = $coursesQuery->paginate(6)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('courses')->get();

        return view('dashboard.user', compact(
            'enrollments',
            'courses',
            'categories',
            'lastLessonProgress',
            'lastQuizAttempt',
            'certificates',
            'learningHistory',
            'notifications',
            'stats'
        ));
    }

    protected function adminDashboard()
    {
        $stats = [
            'total_courses' => Course::count(),
            'published_courses' => Course::where('status', 'published')->count(),
            'draft_courses' => Course::where('status', 'draft')->count(),
            'total_students' => User::where('role', 'user')->count(),
            'total_enrollments' => Enrollment::count(),
            'total_completions' => Enrollment::where('status', 'completed')->count(),
            'total_quizzes' => Quiz::count(),
            'quiz_pass_rate' => QuizAttempt::where('status', 'submitted')->count() > 0
                ? round((QuizAttempt::where('passed', true)->count() / QuizAttempt::where('status', 'submitted')->count()) * 100, 1)
                : 0,
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->take(10)
            ->get();

        $recentEnrollments = Enrollment::with(['user', 'course' => fn($q) => $q->withTrashed()])
            ->latest('enrolled_at')
            ->take(6)
            ->get();

        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->take(6)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentActivities', 'recentEnrollments', 'recentAttempts'));
    }

    protected function superAdminDashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_superadmins' => User::where('role', 'superadmin')->count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'total_certificates' => Certificate::count(),
            'total_lesson_completions' => LessonProgress::where('status', 'completed')->count(),
            'total_quiz_attempts' => QuizAttempt::count(),
        ];

        $recentLogs = ActivityLog::with('user')
            ->latest('created_at')
            ->take(15)
            ->get();

        $recentUsers = User::latest('created_at')->take(5)->get();

        return view('dashboard.superadmin', compact('stats', 'recentLogs', 'recentUsers'));
    }
}
