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

        // Verify eligibility: all published quizzes in course must be completed and passed (memenuhi syarat nilai)
        $courseQuizIds = $course->quizzes()->where('is_published', true)->pluck('id');
        if ($courseQuizIds->isNotEmpty()) {
            $passedQuizzesCount = \App\Models\QuizAttempt::where('user_id', $user->id)
                ->whereIn('quiz_id', $courseQuizIds)
                ->where('status', 'submitted')
                ->where('passed', true)
                ->distinct('quiz_id')
                ->count('quiz_id');

            if ($passedQuizzesCount < $courseQuizIds->count()) {
                throw new Exception('Sertifikat hanya dapat diterbitkan jika seluruh kuis telah diselesaikan dan memenuhi syarat nilai kelulusan.');
            }
        }

        // Generate unguessable verification code
        do {
            $code = 'BKS-' . strtoupper(Str::random(12));
        } while (Certificate::where('certificate_code', $code)->exists());

        // Generate sequential certificate number for this course
        $certNumber = Certificate::getNextCertificateNumber($course->id);
        while (Certificate::where('course_id', $course->id)->where('certificate_number', $certNumber)->exists()) {
            $certNumber = Certificate::incrementCertificateNumber($certNumber);
        }



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
