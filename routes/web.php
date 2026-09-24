<?php

use App\Http\Controllers\Admin\CourseBuilderController;
use App\Http\Controllers\Admin\FaqCategoryController as AdminFaqCategoryController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Superadmin\ActivityLogController;
use App\Http\Controllers\Superadmin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Belajar Kabupaten Bengkalis
|--------------------------------------------------------------------------
*/

// Public Landing & Courses
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{identifier}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');
Route::get('/pertanyaan-umum', [FaqController::class, 'index'])->name('faqs.index');

// Authentication
Route::get('/captcha', [AuthController::class, 'captcha'])->name('captcha');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Enrollment & Learning Player
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::get('/my/courses', [EnrollmentController::class, 'myCourses'])->name('my.courses');
    Route::get('/my/courses/{course}', [EnrollmentController::class, 'myCourseDetail'])->name('my.courses.show');

    Route::get('/learning/{course}', [LearningController::class, 'coursePlayer'])->name('learning.course');
    Route::get('/learning/{course}/lesson/{lesson}', [LearningController::class, 'lessonPlayer'])->name('learning.lesson');
    Route::post('/learning/lesson/{lesson}/complete', [LearningController::class, 'completeLesson'])->name('learning.lesson.complete');
    Route::post('/learning/lesson/{lesson}/progress', [LearningController::class, 'updateProgress'])->name('learning.lesson.progress');

    // Quiz taking
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quizzes/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quiz.attempt');
    Route::get('/quiz-attempts/{attempt}/take', [QuizController::class, 'take'])->name('quiz.take');
    Route::post('/quiz-attempts/{attempt}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz-attempts/{attempt}/result', [QuizController::class, 'result'])->name('quiz.result');

    // Certificate viewing
    Route::get('/my/certificates', [CertificateController::class, 'index'])->name('my.certificates');
    Route::get('/certificates/{identifier}', [CertificateController::class, 'show'])->name('certificates.show');

    // Profile Management (Edit based on user id)
    Route::get('/profile/{id}', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{id}', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Admin & Superadmin - Course Management & Builder
    Route::middleware('role:admin,superadmin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/courses', [CourseBuilderController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [CourseBuilderController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/builder', [CourseBuilderController::class, 'builder'])->name('courses.builder');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::post('/courses/{course}/toggle-publish', [CourseBuilderController::class, 'togglePublish'])->name('courses.toggle-publish');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

        // Course Modules
        Route::post('/courses/{course}/modules', [CourseBuilderController::class, 'storeModule'])->name('modules.store');
        Route::put('/modules/{module}', [CourseBuilderController::class, 'updateModule'])->name('modules.update');
        Route::delete('/modules/{module}', [CourseBuilderController::class, 'deleteModule'])->name('modules.destroy');

        // Module Lessons
        Route::post('/modules/{module}/lessons', [CourseBuilderController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{lesson}', [CourseBuilderController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}', [CourseBuilderController::class, 'deleteLesson'])->name('lessons.destroy');

        // Quizzes & Questions
        Route::post('/courses/{course}/quizzes', [CourseBuilderController::class, 'storeQuiz'])->name('quizzes.store');
        Route::put('/quizzes/{quiz}', [CourseBuilderController::class, 'updateQuiz'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [CourseBuilderController::class, 'deleteQuiz'])->name('quizzes.destroy');
        Route::post('/quizzes/{quiz}/questions', [CourseBuilderController::class, 'storeQuestion'])->name('questions.store');
        Route::put('/questions/{question}', [CourseBuilderController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('/questions/{question}', [CourseBuilderController::class, 'deleteQuestion'])->name('questions.destroy');

        // FAQs Management (CRUD)
        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::resource('faq-categories', AdminFaqCategoryController::class)->except(['show']);
    });

    // Superadmin - User Management & Audit Activity Logs
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
