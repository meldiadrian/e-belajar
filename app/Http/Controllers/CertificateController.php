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
        $certificates = Certificate::where('user_id', $user->id)
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

        if (request()->wantsJson()) {
            return response()->json($certificate);
        }

        return view('certificates.show', compact('certificate'));
    }

    public function verify($code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['user:id,name,institution', 'course:id,title,duration,category_id', 'course.category:id,name'])
            ->first();

        $isValid = ($certificate !== null);

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

        if (request()->wantsJson()) {
            if (!$isValid) {
                return response()->json([
                    'is_valid' => false,
                    'message' => 'Sertifikat tidak ditemukan atau tidak valid.',
                ], 404);
            }

            return response()->json($verificationData);
        }

        return view('certificates.verify', compact('isValid', 'verificationData', 'certificate', 'code'));
    }
}
