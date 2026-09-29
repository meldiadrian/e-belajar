<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $certificates = Certificate::visibleToUser($user->id)
            ->with('course')
            ->latest('issued_at')
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($certificates);
        }

        return view('user.certificates', compact('certificates'));
    }

    public function show($identifier)
    {
        $certificate = Certificate::where('id', $identifier)
            ->orWhere('certificate_code', $identifier)
            ->with(['user', 'course'])
            ->firstOrFail();

        $currentUser = Auth::user();

        // Security check: only recipient or admin can view the full certificate doc
        if (!$currentUser || ($currentUser->id !== $certificate->user_id && !$currentUser->isAdmin())) {
            return redirect()->route('certificates.verify', $certificate->certificate_code);
        }

        // Syarat mengikuti kursus terdahulu dan nilai kelulusan kuis: pastikan seluruh syarat terpenuhi untuk role user
        $course = $certificate->course;
        if ($course && !$currentUser->isAdmin()) {
            if (!$certificate->isEligibleForUser($currentUser)) {
                return redirect()->route('learning.course', $course->slug ?? $course->id)
                    ->with('error', 'Sertifikat belum dapat ditampilkan. Anda harus mengikuti kursus terlebih dahulu hingga selesai (100%) dan menyelesaikan kuis kelulusan.');
            }
        }

        if (request()->wantsJson()) {
            return response()->json($certificate);
        }

        $signer = \App\Models\CertificateSetting::getActive();

        return view('certificates.show', compact('certificate', 'signer'));
    }

    public function verify(Request $request, $code = 'SAMPLE')
    {
        $input = $request->query('code') ?: ($code ?: 'SAMPLE');
        $input = trim(urldecode($input));

        $certificate = null;
        if ($input !== 'SAMPLE' && $input !== '') {
            $certificate = Certificate::where('certificate_code', $input)
                ->orWhere('certificate_number', $input)
                ->with(['user:id,name,institution', 'course:id,title,duration,category_id', 'course.category:id,name'])
                ->first();
        }

        $isValid = ($certificate !== null);
        $displayCode = ($input !== 'SAMPLE' && $input !== '') ? $input : 'SAMPLE';

        $verificationData = [
            'is_valid' => $isValid,
            'certificate_number' => $certificate?->certificate_number,
            'certificate_code' => $certificate?->certificate_code,
            'issued_at' => $certificate?->issued_at?->translatedFormat('d F Y'),
            'recipient_name' => $certificate?->user?->name,
            'institution' => $certificate?->user?->institution ?? 'Masyarakat Umum',
            'course_title' => $certificate?->course?->title,
            'category' => $certificate?->course?->category?->name,
            'duration_hours' => $certificate?->course?->duration ? round($certificate->course->duration / 60, 1) . ' Jam' : '-',
            'issuer' => 'Pemerintah Kabupaten Bengkalis - E-Belajar Platform',
        ];

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Sertifikat tidak ditemukan atau tidak valid.',
                ], 404);
            }

            return response()->json($verificationData);
        }

        return view('certificates.verify', [
            'isValid' => $isValid,
            'verificationData' => $verificationData,
            'certificate' => $certificate,
            'code' => $displayCode,
        ]);
    }
}
