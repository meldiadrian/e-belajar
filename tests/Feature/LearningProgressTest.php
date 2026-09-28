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

    public function test_quiz_score_must_meet_passing_score_for_certificate_and_allows_unlimited_retakes(): void
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
            'max_attempts' => 1, // Diberi batas 1 untuk menguji bahwa jika tidak lulus, dapat mengulangi tanpa batas
            'is_published' => true,
        ]);

        $question = \App\Models\Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Soal Cloud',
            'type' => 'true_false',
            'points' => 100,
        ]);

        $optCorrect = \App\Models\QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Jawaban Benar',
            'is_correct' => true,
        ]);

        $optWrong = \App\Models\QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Jawaban Salah',
            'is_correct' => false,
        ]);

        // Complete lesson
        $this->actingAs($user)->post(route('learning.lesson.complete', $lesson->id));

        // Start attempt 1 and submit with failing score (0%)
        $quizService = app(\App\Services\QuizService::class);
        $attempt1 = $quizService->startAttempt($user, $quiz);
        $quizService->submitAttempt($attempt1, [$question->id => $optWrong->id]);

        $attempt1->refresh();
        $this->assertFalse((bool) $attempt1->passed);
        $this->assertEquals(0, $attempt1->score);

        // Certificate MUST NOT be generated because passing score requirement was not met
        $this->assertDatabaseMissing('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Result page must NOT show certificate button
        $resultResponse = $this->actingAs($user)->get(route('quiz.result', $attempt1->id));
        $resultResponse->assertOk();
        $resultResponse->assertSee('TIDAK LULUS');
        $resultResponse->assertDontSee('Buka Lembar Sertifikat');

        // Retake: Although max_attempts was 1, user can retake without limit because they haven't passed
        $attempt2 = $quizService->startAttempt($user, $quiz);
        $this->assertNotNull($attempt2);
        $this->assertEquals(2, $attempt2->attempt_number);

        // Submit attempt 2 with correct answer (100% >= 80%)
        $quizService->submitAttempt($attempt2, [$question->id => $optCorrect->id]);
        $attempt2->refresh();
        $this->assertTrue((bool) $attempt2->passed);

        // Certificate MUST now be generated
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        // Result page displays Buka Lembar Sertifikat
        $resultResponse2 = $this->actingAs($user)->get(route('quiz.result', $attempt2->id));
        $resultResponse2->assertOk();
        $resultResponse2->assertSee('Buka Lembar Sertifikat');
    }
}
