<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'certificate_code',
        'issued_at',
        'certificate_url',
        'verification_url',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope query to only include certificates that are eligible to be displayed for the user.
     * The user must have followed and completed the course (100% progress) and passed all published quizzes.
     */
    public function scopeVisibleToUser(Builder $query, int $userId): Builder
    {
        return $query->where('certificates.user_id', $userId)
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                  ->from('course_progress')
                  ->whereColumn('course_progress.user_id', 'certificates.user_id')
                  ->whereColumn('course_progress.course_id', 'certificates.course_id')
                  ->where(function ($sub) {
                      $sub->where('course_progress.progress_percentage', '>=', 100)
                          ->orWhere('course_progress.status', 'completed');
                  });
            })
            ->whereNotExists(function ($q) {
                $q->selectRaw(1)
                  ->from('quizzes')
                  ->whereColumn('quizzes.course_id', 'certificates.course_id')
                  ->where('quizzes.is_published', true)
                  ->whereNotExists(function ($sub) {
                      $sub->selectRaw(1)
                          ->from('quiz_attempts')
                          ->whereColumn('quiz_attempts.user_id', 'certificates.user_id')
                          ->whereColumn('quiz_attempts.quiz_id', 'quizzes.id')
                          ->where('quiz_attempts.status', 'submitted')
                          ->where('quiz_attempts.passed', true);
                  });
            });
    }

    /**
     * Check if the certificate is eligible to be displayed for the user.
     * User must have followed and completed the course (progress 100%) and passed all published quizzes.
     */
    public function isEligibleForUser(?User $user = null): bool
    {
        $user = $user ?? $this->user ?? User::find($this->user_id);
        if (!$user) {
            return false;
        }

        $courseId = $this->course_id;
        if (!$courseId) {
            return false;
        }

        // 1. User must follow and complete the course (100% progress)
        $progress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if (!$progress || ((float) $progress->progress_percentage < 100.0 && $progress->status !== 'completed')) {
            return false;
        }

        // 2. User must complete and pass all published quizzes in the course
        $course = $this->course ?? Course::find($courseId);
        if ($course) {
            $courseQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
            if ($courseQuizIds->isNotEmpty()) {
                $passedQuizzesCount = QuizAttempt::where('user_id', $user->id)
                    ->whereIn('quiz_id', $courseQuizIds)
                    ->where('status', 'submitted')
                    ->where('passed', true)
                    ->distinct('quiz_id')
                    ->count('quiz_id');

                if ($passedQuizzesCount < $courseQuizIds->count()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Increment the numeric index within a certificate number string while preserving formatting.
     */
    public static function incrementCertificateNumber(string $number): string
    {
        // Case 1: Trailing is 4-digit year, index is placed before the year (e.g., 001/BKPSDM/2026 or 420/005/DISDIK/2026)
        if (preg_match('/^(.*?)(\d+)([^\d]+(?:19|20)\d{2})$/', $number, $matches)) {
            $prefix = $matches[1];
            $index = $matches[2];
            $suffix = $matches[3];
            $nextIndex = str_pad((int) $index + 1, strlen($index), '0', STR_PAD_LEFT);
            return $prefix . $nextIndex . $suffix;
        }

        // Case 2: Trailing digits is the serial index (e.g., BKPP-PKA/2026/00001, CERT/BKS/2026/09/00002, SERT-0001, 100)
        if (preg_match('/^(.*?)(\d+)$/', $number, $matches)) {
            $prefix = $matches[1];
            $index = $matches[2];
            $nextIndex = str_pad((int) $index + 1, strlen($index), '0', STR_PAD_LEFT);
            return $prefix . $nextIndex;
        }

        return $number . '-00001';
    }

    /**
     * Reset the numeric index within a certificate number string back to 1.
     */
    public static function resetCertificateNumberIndex(string $number): string
    {
        // Case 1: Trailing is 4-digit year, index is placed before the year (e.g., 005/BKPSDM/2026 or 420/005/DISDIK/2026)
        if (preg_match('/^(.*?)(\d+)([^\d]+(?:19|20)\d{2})$/', $number, $matches)) {
            $prefix = $matches[1];
            $index = $matches[2];
            $suffix = $matches[3];
            $resetIndex = str_pad('1', strlen($index), '0', STR_PAD_LEFT);
            return $prefix . $resetIndex . $suffix;
        }

        // Case 2: Trailing digits is the serial index (e.g., BKPP-PKA/2026/00004, CERT/BKS/2026/09/00002, SERT-0001, 100)
        if (preg_match('/^(.*?)(\d+)$/', $number, $matches)) {
            $prefix = $matches[1];
            $index = $matches[2];
            $resetIndex = str_pad('1', strlen($index), '0', STR_PAD_LEFT);
            return $prefix . $resetIndex;
        }

        return $number;
    }

    /**
     * Generate the next sequential certificate number, optionally scoped to a specific course.
     */
    public static function getNextCertificateNumber(?int $courseId = null): string
    {
        if ($courseId) {
            $courseLatest = static::where('course_id', $courseId)->latest('id')->first();
            if ($courseLatest && !empty($courseLatest->certificate_number)) {
                return static::incrementCertificateNumber($courseLatest->certificate_number);
            }

            // Jika kursus pelatihan ini belum memiliki sertifikat, gunakan format sertifikat yang ada mulai dari awal
            $overallLatest = static::latest('id')->first();
            if ($overallLatest && !empty($overallLatest->certificate_number)) {
                return static::resetCertificateNumberIndex($overallLatest->certificate_number);
            }
        } else {
            $latest = static::latest('id')->first();
            if ($latest && !empty($latest->certificate_number)) {
                return static::incrementCertificateNumber($latest->certificate_number);
            }
        }

        $year = date('Y');
        $month = date('m');
        return "CERT/BKS/{$year}/{$month}/00001";
    }
}

