<?php

namespace App\Services;

use App\Models\CourseProgress;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;
use Exception;

class QuizService
{
    protected ProgressService $progressService;

    public function __construct(ProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Start a new quiz attempt for a user.
     */
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        $existingAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        // Cek apakah peserta sudah pernah lulus kuis ini
        $hasPassed = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('passed', true)
            ->exists();

        // Jika belum lulus, peserta berhak mengulangi kuis/kelas tanpa batas percobaan
        if ($hasPassed && $quiz->max_attempts > 0 && $existingAttempts >= $quiz->max_attempts) {
            throw new Exception("Batas maksimal percobaan ({$quiz->max_attempts}x) untuk kuis ini telah tercapai.");
        }

        // Check if there is an in-progress attempt already
        $currentInProgress = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($currentInProgress) {
            return $currentInProgress;
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'attempt_number' => $existingAttempts + 1,
            'started_at' => now(),
            'score' => 0,
            'max_score' => 0,
            'percentage' => 0,
            'passed' => false,
            'status' => 'in_progress',
        ]);

        ActivityLogService::log(
            action: 'quiz_started',
            entity: $quiz,
            description: "Peserta {$user->name} memulai percobaan #{$attempt->attempt_number} pada kuis '{$quiz->title}'.",
            user: $user
        );

        return $attempt;
    }

    /**
     * Get quiz data for an ongoing attempt with correct answers stripped out (Anti-cheat).
     */
    public function getQuizForStudent(Quiz $quiz): array
    {
        $questionsQuery = $quiz->questions()->with(['options' => function ($q) use ($quiz) {
            if ($quiz->shuffle_options) {
                $q->inRandomOrder();
            } else {
                $q->orderBy('sort_order');
            }
        }]);

        if ($quiz->shuffle_questions) {
            $questions = $questionsQuery->inRandomOrder()->get();
        } else {
            $questions = $questionsQuery->orderBy('sort_order')->get();
        }

        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'time_limit' => $quiz->time_limit,
            'passing_score' => $quiz->passing_score,
            'questions' => $questions->map(function (Question $q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'type' => $q->type,
                    'points' => $q->points,
                    'sort_order' => $q->sort_order,
                    // DO NOT include explanation or is_correct
                    'options' => $q->options->map(function (QuestionOption $opt) {
                        return [
                            'id' => $opt->id,
                            'option_text' => $opt->option_text,
                            'sort_order' => $opt->sort_order,
                            // CRITICAL: is_correct is intentionally excluded
                        ];
                    }),
                ];
            }),
        ];
    }

    /**
     * Submit an attempt, grade the answers, calculate score & percentage.
     */
    public function submitAttempt(QuizAttempt $attempt, array $submittedAnswers): QuizAttempt
    {
        if ($attempt->status === 'submitted') {
            return $attempt;
        }

        $quiz = $attempt->quiz()->with('questions.options')->first();
        $totalEarned = 0.0;
        $totalMax = 0.0;

        foreach ($quiz->questions as $question) {
            $totalMax += $question->points;
            $userAns = $submittedAnswers[$question->id] ?? null;

            $isCorrect = false;
            $pointsEarned = 0.0;
            $selectedOptionId = null;
            $answerText = null;

            if ($question->type === 'single_choice' || $question->type === 'true_false') {
                $selectedOptionId = is_numeric($userAns) ? (int) $userAns : null;
                if ($selectedOptionId) {
                    $option = $question->options->firstWhere('id', $selectedOptionId);
                    if ($option) {
                        if ($option->is_correct) {
                            $isCorrect = true;
                            $pointsEarned = (float) $question->points;
                        }
                    } else {
                        $selectedOptionId = null;
                    }
                }
            } elseif ($question->type === 'multiple_choice') {
                // userAns is array of option IDs
                $selectedOptionIds = is_array($userAns) ? array_map('intval', $userAns) : [];
                $correctOptionIds = $question->options->where('is_correct', true)->pluck('id')->all();
                sort($selectedOptionIds);
                sort($correctOptionIds);

                if (!empty($selectedOptionIds) && $selectedOptionIds === $correctOptionIds) {
                    $isCorrect = true;
                    $pointsEarned = (float) $question->points;
                }
                $answerText = json_encode($selectedOptionIds);
            } elseif ($question->type === 'short_answer') {
                $answerText = is_string($userAns) ? trim($userAns) : '';
                // Check if any correct option matches the text
                $correctTexts = $question->options->where('is_correct', true)->pluck('option_text');
                foreach ($correctTexts as $cText) {
                    if (strcasecmp(trim($cText), $answerText) === 0) {
                        $isCorrect = true;
                        $pointsEarned = (float) $question->points;
                        break;
                    }
                }
            } elseif ($question->type === 'essay') {
                $answerText = is_string($userAns) ? trim($userAns) : '';
                // Essay awarded base points upon submission
                $isCorrect = !empty($answerText);
                $pointsEarned = $isCorrect ? (float) $question->points : 0.0;
            }

            $totalEarned += $pointsEarned;

            // Record answer
            QuizAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'question_option_id' => $selectedOptionId,
                'answer_text' => $answerText,
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);
        }

        $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 2) : 0.0;
        $passingScore = (float) ($quiz->passing_score ?? 0);
        // Syarat nilai kelulusan kuis
        $passed = ($percentage >= $passingScore);

        $attempt->update([
            'score' => $totalEarned,
            'max_score' => $totalMax,
            'percentage' => $percentage,
            'passed' => $passed,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $user = $attempt->user;

        if ($passed) {
            ActivityLogService::log(
                action: 'quiz_submitted',
                entity: $quiz,
                description: "Peserta {$user->name} menyelesaikan kuis '{$quiz->title}' dengan nilai {$totalEarned}/{$totalMax} ({$percentage}%) - LULUS (Syarat: {$passingScore}%)",
                user: $user
            );

            NotificationService::send(
                user: $user,
                title: 'Kuis Selesai & Lulus!',
                message: "Selamat! Nilai kuis '{$quiz->title}': {$percentage}% (Lulus - Memenuhi syarat kelulusan {$passingScore}%).",
                type: 'success',
                data: ['quiz_id' => $quiz->id, 'attempt_id' => $attempt->id, 'score' => $percentage, 'passed' => true]
            );

            // Jika kuis terkait suatu lesson, selesaikan lesson jika lulus
            if ($quiz->lesson_id) {
                $lesson = $quiz->lesson;
                if ($lesson) {
                    $this->progressService->completeLesson($user, $lesson);
                }
            }

            // Periksa apakah progres kursus 100% dan seluruh kuis kursus telah lulus untuk penerbitan sertifikat
            $course = $quiz->course;
            if ($course && $course->certificate_enabled) {
                $cProgress = CourseProgress::where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->first();

                $publishedQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
                $passedQuizzesCount = QuizAttempt::where('user_id', $user->id)
                    ->whereIn('quiz_id', $publishedQuizIds)
                    ->where('status', 'submitted')
                    ->where('passed', true)
                    ->distinct('quiz_id')
                    ->count('quiz_id');

                $allQuizzesPassed = ($passedQuizzesCount >= $publishedQuizIds->count());

                if ($cProgress && (float) $cProgress->progress_percentage >= 100.0 && $allQuizzesPassed) {
                    try {
                        $certificateService = app(CertificateService::class);
                        $certificateService->generateCertificate($user, $course);
                    } catch (\Throwable $e) {
                        // Log or ignore
                    }
                }
            }
        } else {
            ActivityLogService::log(
                action: 'quiz_submitted',
                entity: $quiz,
                description: "Peserta {$user->name} menyelesaikan kuis '{$quiz->title}' dengan nilai {$totalEarned}/{$totalMax} ({$percentage}%) - TIDAK LULUS (Syarat: {$passingScore}%)",
                user: $user
            );

            NotificationService::send(
                user: $user,
                title: 'Kuis Belum Memenuhi Syarat Kelulusan',
                message: "Nilai kuis '{$quiz->title}': {$percentage}%. Belum memenuhi syarat minimal kelulusan ({$passingScore}%). Anda dapat mengulangi kuis tanpa batas percobaan.",
                type: 'warning',
                data: ['quiz_id' => $quiz->id, 'attempt_id' => $attempt->id, 'score' => $percentage, 'passed' => false]
            );
        }

        return $attempt->fresh(['answers.question.options']);
    }
}
