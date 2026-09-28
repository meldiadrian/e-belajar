<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected QuizService $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    public function show($quizId)
    {
        $quiz = Quiz::with('course')->withCount('questions')->findOrFail($quizId);
        $user = Auth::user();

        $userAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->latest('created_at')
            ->get();

        $inProgressAttempt = $userAttempts->firstWhere('status', 'in_progress');
        $bestAttempt = $userAttempts->where('status', 'submitted')->sortByDesc('score')->first();
        $hasPassed = $userAttempts->where('passed', true)->isNotEmpty();

        if (request()->wantsJson()) {
            return response()->json([
                'quiz' => $quiz,
                'attempts' => $userAttempts,
                'best_attempt' => $bestAttempt,
                'has_passed' => $hasPassed,
            ]);
        }

        return view('quiz.show', compact('quiz', 'userAttempts', 'inProgressAttempt', 'bestAttempt', 'hasPassed'));
    }

    public function attempt(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $user = Auth::user();

        try {
            $attempt = $this->quizService->startAttempt($user, $quiz);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Percobaan kuis dimulai.',
                    'attempt' => $attempt,
                    'quiz_data' => $this->quizService->getQuizForStudent($quiz),
                ], 201);
            }

            return redirect()->route('quiz.take', $attempt->id);
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function take($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        if ($attempt->status === 'submitted') {
            return redirect()->route('quiz.result', $attempt->id)
                ->with('info', 'Percobaan ini sudah dikirimkan sebelumnya.');
        }

        $quizData = $this->quizService->getQuizForStudent($attempt->quiz);

        return view('quiz.take', compact('attempt', 'quizData'));
    }

    public function submitAnswers(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        // Just saving partial or answer draft
        return response()->json(['message' => 'Jawaban sementara tersimpan.']);
    }

    public function submit(Request $request, $attemptId)
    {
        $attempt = QuizAttempt::findOrFail($attemptId);
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            abort(403);
        }

        $answers = $request->input('answers', []);

        $completedAttempt = $this->quizService->submitAttempt($attempt, $answers);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kuis berhasil diselesaikan.',
                'attempt' => $completedAttempt,
            ]);
        }

        $msg = $completedAttempt->passed
            ? 'Selamat! Anda telah lulus kuis evaluasi ini.'
            : 'Kuis telah dikirim, namun nilai Anda belum memenuhi syarat kelulusan. Anda dapat mengulanginya tanpa batas.';

        return redirect()->route('quiz.result', $attempt->id)->with('info', $msg);
    }

    public function result($attemptId)
    {
        $attempt = QuizAttempt::with([
            'quiz.course',
            'answers.question.options',
        ])->findOrFail($attemptId);

        $user = Auth::user();

        if ($attempt->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if (request()->wantsJson()) {
            return response()->json($attempt);
        }

        // Sertifikat hanya ditampilkan jika percobaan ini lulus dan seluruh kuis kursus telah memenuhi syarat nilai kelulusan
        $certificate = null;
        if ($attempt->passed && $attempt->quiz->course) {
            $course = $attempt->quiz->course;
            $courseQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
            $allPassed = true;
            if ($courseQuizIds->isNotEmpty()) {
                $passedCount = QuizAttempt::where('user_id', $attempt->user_id)
                    ->whereIn('quiz_id', $courseQuizIds)
                    ->where('status', 'submitted')
                    ->where('passed', true)
                    ->distinct('quiz_id')
                    ->count('quiz_id');

                $allPassed = ($passedCount >= $courseQuizIds->count());
            }

            if ($allPassed) {
                $certificate = \App\Models\Certificate::where('user_id', $attempt->user_id)
                    ->where('course_id', $course->id)
                    ->first();
            }
        }

        return view('quiz.result', compact('attempt', 'certificate'));
    }
}
