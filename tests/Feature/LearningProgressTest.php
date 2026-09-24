<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CertificateService;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_progress_is_saved_and_course_progress_calculated_correctly(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Administrasi', 'slug' => 'administrasi']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Tata Kelola Administrasi',
            'slug' => 'tata-kelola-administrasi',
            'status' => 'published',
            'level' => 'beginner',
            'certificate_enabled' => true,
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson1 = Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Pelajaran 1',
            'slug' => 'pelajaran-1',
            'lesson_type' => 'text',
            'duration' => 300,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson2 = Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Pelajaran 2',
            'slug' => 'pelajaran-2',
            'lesson_type' => 'text',
            'duration' => 300,
            'sort_order' => 2,
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $progressService = app(ProgressService::class);

        // 1. Complete lesson 1
        $progressService->completeLesson($user, $lesson1);

        $this->assertDatabaseHas('lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson1->id,
            'status' => 'completed',
            'progress_percentage' => 100.0,
        ]);

        // Course has 2 lessons, 1 completed = 50%
        $this->assertDatabaseHas('course_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'completed_lessons' => 1,
            'total_lessons' => 2,
            'progress_percentage' => 50.0,
            'status' => 'in_progress',
        ]);

        // 2. Complete lesson 2
        $progressService->completeLesson($user, $lesson2);

        // Course has 2 lessons, 2 completed = 100% -> status completed
        $this->assertDatabaseHas('course_progress', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'completed_lessons' => 2,
            'total_lessons' => 2,
            'progress_percentage' => 100.0,
            'status' => 'completed',
        ]);

        // Enrollment should be marked completed
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'completed',
        ]);

        // Certificate should be automatically issued
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_course_progress_safe_division_when_total_lessons_is_zero(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'Umum', 'slug' => 'umum']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Kursus Kosong',
            'slug' => 'kursus-kosong',
            'status' => 'published',
            'level' => 'beginner',
        ]);

        $progressService = app(ProgressService::class);
        $progress = $progressService->recalculateCourseProgress($user, $course);

        $this->assertEquals(0, $progress->total_lessons);
        $this->assertEquals(0, $progress->progress_percentage);
    }

    public function test_after_completing_lesson_user_is_directed_to_quiz_and_certificate_only_available_after_quiz_passed(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Pemrograman Web Modern',
            'slug' => 'pemrograman-web-modern',
            'status' => 'published',
            'level' => 'beginner',
            'certificate_enabled' => true,
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Dasar PHP',
            'slug' => 'dasar-php',
            'lesson_type' => 'text',
            'duration' => 300,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $quiz = \App\Models\Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Kuis Evaluasi PHP',
            'passing_score' => 70,
            'is_published' => true,
        ]);

        $question = \App\Models\Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Apakah PHP bahasa server-side?',
            'type' => 'true_false',
            'points' => 100,
        ]);

        $optCorrect = \App\Models\QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Ya, benar',
            'is_correct' => true,
        ]);

        // 1. User clicks "Telah Selesai" / "Tandai Selesai" on lesson
        $response = $this->actingAs($user)->post(route('learning.lesson.complete', $lesson->id));

        // Must redirect to quiz
        $response->assertRedirect(route('quiz.show', $quiz->id));

        // Certificate MUST NOT be issued yet because quiz is pending
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // 2. User starts the quiz
        $quizService = app(\App\Services\QuizService::class);
        $attempt = $quizService->startAttempt($user, $quiz);

        // 3. User submits quiz answers and passes
        $quizService->submitAttempt($attempt, [$question->id => $optCorrect->id]);

        $attempt->refresh();
        $this->assertTrue((bool) $attempt->passed);

        // Now certificate MUST be generated and available
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Check result page displays certificate
        $resultResponse = $this->actingAs($user)->get(route('quiz.result', $attempt->id));
        $resultResponse->assertOk();
        $resultResponse->assertSee('Buka Sertifikat');
    }

    public function test_quiz_score_is_ignored_so_certificate_can_be_issued_even_with_low_or_failing_score(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Dasar Cloud Computing',
            'slug' => 'dasar-cloud-computing',
            'status' => 'published',
            'level' => 'beginner',
            'certificate_enabled' => true,
        ]);

        $module = CourseModule::create([
            'course_id' => $course->id,
            'title' => 'Modul 1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson = Lesson::create([
            'course_module_id' => $module->id,
            'title' => 'Intro Cloud',
            'slug' => 'intro-cloud',
            'lesson_type' => 'text',
            'duration' => 300,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $quiz = \App\Models\Quiz::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Kuis Cloud',
            'passing_score' => 80,
            'is_published' => true,
        ]);

        $question = \App\Models\Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Soal Cloud',
            'type' => 'true_false',
            'points' => 100,
        ]);

        $optWrong = \App\Models\QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Jawaban Salah',
            'is_correct' => false,
        ]);

        // Complete lesson
        $this->actingAs($user)->post(route('learning.lesson.complete', $lesson->id));

        // Start and submit quiz with failing score (0%)
        $quizService = app(\App\Services\QuizService::class);
        $attempt = $quizService->startAttempt($user, $quiz);
        $quizService->submitAttempt($attempt, [$question->id => $optWrong->id]);

        $attempt->refresh();
        $this->assertTrue((bool) $attempt->passed);
        $this->assertEquals(0, $attempt->score);

        // Certificate MUST STILL be generated because score is ignored for certificate issuance
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Result page displays Buka Sertifikat
        $resultResponse = $this->actingAs($user)->get(route('quiz.result', $attempt->id));
        $resultResponse->assertOk();
        $resultResponse->assertSee('Buka Sertifikat');
    }
}
