<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate an official certificate for a user upon course completion.
     */
    public function generateCertificate(User $user, Course $course): Certificate
    {
        // Check if certificate already exists
        $existing = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Verify eligibility
        if (!$course->certificate_enabled) {
            throw new Exception('Sertifikat tidak diaktifkan untuk kursus ini.');
        }

        $progress = CourseProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$progress || (float) $progress->progress_percentage < 100.0) {
            throw new Exception('Sertifikat hanya dapat diterbitkan jika progres kursus telah mencapai 100%.');
        }

        // Verify eligibility: all published quizzes in course must be completed (nilai diabaikan untuk penerbitan sertifikat)
        $courseQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
        if ($courseQuizIds->isNotEmpty()) {
            $completedQuizzesCount = \App\Models\QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $courseQuizIds)
                ->where(function ($q) {
                    $q->where('status', 'submitted')
                      ->orWhere('passed', true);
                })
                ->distinct('quiz_id')
                ->count('quiz_id');

            if ($completedQuizzesCount < $courseQuizIds->count()) {
                throw new Exception('Sertifikat hanya dapat diterbitkan setelah seluruh kuis evaluasi diselesaikan.');
            }
        }

        // Generate unguessable verification code
        do {
            $code = 'BKS-' . strtoupper(Str::random(12));
        } while (Certificate::where('certificate_code', $code)->exists());

        // Generate serial certificate number
        $year = date('Y');
        $month = date('m');
        $count = Certificate::whereYear('created_at', $year)->count() + 1;
        $serial = str_pad((string) $count, 5, '0', STR_PAD_LEFT);
        $certNumber = "CERT/BKS/{$year}/{$month}/{$serial}";

        $verificationUrl = url("/certificates/verify/{$code}");

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'certificate_number' => $certNumber,
            'certificate_code' => $code,
            'issued_at' => now(),
            'certificate_url' => url("/certificates/{$code}"),
            'verification_url' => $verificationUrl,
        ]);

        // Audit log
        ActivityLogService::log(
            action: 'certificate_issued',
            entity: $certificate,
            description: "Sertifikat resmi diterbitkan untuk {$user->name} pada kursus {$course->title}.",
            user: $user
        );

        // Notification
        NotificationService::send(
            user: $user,
            title: 'Sertifikat Kelulusan Tersedia!',
            message: "Selamat! Sertifikat kelulusan Anda untuk kursus '{$course->title}' telah diterbitkan.",
            type: 'success',
            data: ['certificate_id' => $certificate->id, 'certificate_code' => $code]
        );

        return $certificate;
    }
}
