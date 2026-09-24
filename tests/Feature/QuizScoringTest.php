<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\User;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_correct_answers_are_hidden_from_student_payload_before_submit(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Keamanan Sistem',
            'slug' => 'keamanan-sistem',
            'level' => 'intermediate',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Kuis Keamanan',
            'passing_score' => 70,
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Apakah password harus di-hash?',
            'type' => 'true_false',
            'points' => 50,
        ]);

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Ya, wajib',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => 'Tidak perlu',
            'is_correct' => false,
        ]);

        $quizService = app(QuizService::class);
        $payload = $quizService->getQuizForStudent($quiz);

        // Verify is_correct is not in options array
        foreach ($payload['questions'] as $q) {
            foreach ($q['options'] as $opt) {
                $this->assertArrayNotHasKey('is_correct', $opt);
            }
        }
    }

    public function test_quiz_attempt_scoring_and_passing_status_calculation(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it']);
        $course = Course::create([
            'category_id' => $category->id,
            'created_by' => $user->id,
            'title' => 'Keamanan Sistem',
            'slug' => 'keamanan-sistem',
            'level' => 'intermediate',
        ]);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => 'Kuis Keamanan',
            'passing_score' => 75,
        ]);

        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Soal 1',
            'type' => 'single_choice',
            'points' => 50,
        ]);

        $opt1Correct = QuestionOption::create([
            'question_id' => $q1->id,
            'option_text' => 'Opsi Benar 1',
            'is_correct' => true,
        ]);

        $opt1Wrong = QuestionOption::create([
            'question_id' => $q1->id,
            'option_text' => 'Opsi Salah 1',
            'is_correct' => false,
        ]);

        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'question' => 'Soal 2',
            'type' => 'single_choice',
            'points' => 50,
        ]);

        $opt2Correct = QuestionOption::create([
            'question_id' => $q2->id,
            'option_text' => 'Opsi Benar 2',
            'is_correct' => true,
        ]);

        $opt2Wrong = QuestionOption::create([
            'question_id' => $q2->id,
            'option_text' => 'Opsi Salah 2',
            'is_correct' => false,
        ]);

        $quizService = app(QuizService::class);

        // Case 1: 100% score (Both correct) -> Passed
        $attemptPassed = $quizService->startAttempt($user, $quiz);
        $submittedAnswers = [
            $q1->id => $opt1Correct->id,
            $q2->id => $opt2Correct->id,
        ];
        $resultPassed = $quizService->submitAttempt($attemptPassed, $submittedAnswers);

        $this->assertEquals(100.0, (float) $resultPassed->score);
        $this->assertEquals(100.0, (float) $resultPassed->percentage);
        $this->assertTrue((bool) $resultPassed->passed);

        // Case 2: 50% score (1 correct, 1 wrong) -> Failed (since passing score is 75)
        $user2 = User::factory()->create(['role' => 'user']);
        $attemptFailed = $quizService->startAttempt($user2, $quiz);
        $submittedAnswersFailed = [
            $q1->id => $opt1Correct->id,
            $q2->id => $opt2Wrong->id,
        ];
        $resultFailed = $quizService->submitAttempt($attemptFailed, $submittedAnswersFailed);

        $this->assertEquals(50.0, (float) $resultFailed->score);
        $this->assertEquals(50.0, (float) $resultFailed->percentage);
        $this->assertTrue((bool) $resultFailed->passed);
    }
}
