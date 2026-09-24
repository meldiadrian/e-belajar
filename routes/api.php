<?php

use App\Http\Controllers\Admin\CourseBuilderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - E-Belajar Kabupaten Bengkalis
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth');

// Public Course browsing & certificate verification
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/courses/{course}/modules', [CourseBuilderController::class, 'getModules']);
Route::get('/modules/{module}/lessons', [CourseBuilderController::class, 'getLessons']);
Route::get('/lessons/{id}', [CourseBuilderController::class, 'getLesson']);
Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify']);

// Authenticated Endpoints
Route::middleware('auth')->group(function () {
    // Enrollment
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('/my/courses', [EnrollmentController::class, 'myCourses']);
    Route::get('/my/courses/{course}', [EnrollmentController::class, 'myCourseDetail']);

    // Progress
    Route::get('/courses/{course}/progress', [LearningController::class, 'getCourseProgress']);
    Route::post('/lessons/{lesson}/progress', [LearningController::class, 'updateProgress']);
    Route::post('/lessons/{lesson}/complete', [LearningController::class, 'completeLesson']);

    // Quiz
    Route::get('/quizzes/{quiz}', [QuizController::class, 'show']);
    Route::post('/quizzes/{quiz}/attempt', [QuizController::class, 'attempt']);
    Route::post('/quiz-attempts/{attempt}/answers', [QuizController::class, 'submitAnswers']);
    Route::post('/quiz-attempts/{attempt}/submit', [QuizController::class, 'submit']);
    Route::get('/quiz-attempts/{attempt}/result', [QuizController::class, 'result']);

    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{id}', [CertificateController::class, 'show']);

    // Profile
    Route::get('/profile/{id}', [\App\Http\Controllers\ProfileController::class, 'show']);
    Route::put('/profile/{id}', [\App\Http\Controllers\ProfileController::class, 'update']);

    // Admin & Superadmin Course Management
    Route::middleware('role:admin,superadmin')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

        Route::post('/courses/{course}/modules', [CourseBuilderController::class, 'storeModule']);
        Route::put('/modules/{id}', [CourseBuilderController::class, 'updateModule']);
        Route::delete('/modules/{id}', [CourseBuilderController::class, 'deleteModule']);

        Route::post('/modules/{module}/lessons', [CourseBuilderController::class, 'storeLesson']);
        Route::put('/lessons/{id}', [CourseBuilderController::class, 'updateLesson']);
        Route::delete('/lessons/{id}', [CourseBuilderController::class, 'deleteLesson']);
    });
});
