<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IssuedCertificateController extends Controller
{
    /**
     * Display a listing of issued certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course'])
            ->where(function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->selectRaw(1)
                        ->from('enrollments')
                        ->whereColumn('enrollments.user_id', 'certificates.user_id')
                        ->whereColumn('enrollments.course_id', 'certificates.course_id');
                })->orWhereExists(function ($sub) {
                    $sub->selectRaw(1)
                        ->from('course_progress')
                        ->whereColumn('course_progress.user_id', 'certificates.user_id')
                        ->whereColumn('course_progress.course_id', 'certificates.course_id');
                });
            })
            ->latest('issued_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhere('certificate_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $certificates = $query->paginate(15)->withQueryString();

        return view('admin.issued-certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'nip', 'email', 'role']);
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $nextCertificateNumber = Certificate::getNextCertificateNumber();

        // Nomor registrasi berikutnya per kursus pelatihan
        $courseNextNumbers = [];
        foreach ($courses as $c) {
            $courseNextNumbers[$c->id] = Certificate::getNextCertificateNumber($c->id);
        }

        return view('admin.issued-certificates.create', compact('users', 'courses', 'nextCertificateNumber', 'courseNextNumbers'));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request)
    {
        $hasSingleUser = $request->filled('user_id') && $request->user_id !== 'role_user' && is_numeric($request->user_id);

        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
            'certificate_number' => [
                'required',
                'string',
                'max:100',
            ],
            'issued_at' => ['nullable', 'date'],
        ];

        $messages = [
            'course_id.required' => 'Kursus pelatihan wajib dipilih.',
            'course_id.exists' => 'Kursus yang dipilih tidak valid.',
            'certificate_number.required' => 'Nomor sertifikat wajib diisi.',
        ];

        $courseId = $request->input('course_id');

        if ($hasSingleUser) {
            $rules['user_id'] = ['required', 'exists:users,id'];
            $rules['certificate_number'][] = Rule::unique('certificates', 'certificate_number')
                ->where(fn($q) => $q->where('course_id', $courseId));
            $messages['user_id.required'] = 'Peserta penerima sertifikat wajib dipilih.';
            $messages['user_id.exists'] = 'Peserta yang dipilih tidak valid.';
            $messages['certificate_number.unique'] = 'Nomor sertifikat ini sudah digunakan pada kursus ini.';
        } else {
            $rules['role'] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules, $messages);

        $course = Course::findOrFail($courseId);
        $issuedAt = !empty($validated['issued_at']) ? $validated['issued_at'] : now();

        if ($hasSingleUser) {
            $existing = Certificate::where('user_id', $validated['user_id'])
                ->where('course_id', $courseId)
                ->first();

            if ($existing) {
                return back()->withInput()->withErrors([
                    'user_id' => "Peserta ini sudah memiliki sertifikat untuk kursus tersebut (Nomor: {$existing->certificate_number}).",
                ]);
            }

            do {
                $code = 'BKS-' . strtoupper(Str::random(12));
            } while (Certificate::where('certificate_code', $code)->exists());

            $certificate = Certificate::create([
                'user_id' => $validated['user_id'],
                'course_id' => $courseId,
                'certificate_number' => $validated['certificate_number'],
                'certificate_code' => $code,
                'issued_at' => $issuedAt,
                'certificate_url' => url("/certificates/{$code}"),
                'verification_url' => url("/certificates/verify/{$code}"),
            ]);

            $certificate->load(['user', 'course']);

            ActivityLogService::log(
                action: 'certificate_created',
                entity: $certificate,
                description: "Sertifikat nomor '{$certificate->certificate_number}' berhasil diinput untuk peserta '{$certificate->user?->name}' pada kursus '{$certificate->course?->title}'."
            );

            return redirect()->route('admin.issued-certificates.index')
                ->with('success', "Sertifikat nomor '{$certificate->certificate_number}' berhasil diinput.");
        }

        // Penanganan Role User: seluruh pengguna role 'user' otomatis mengikuti penomoran secara berurutan
        $users = User::where('role', 'user')->orderBy('id')->get();

        if ($users->isEmpty()) {
            return back()->withInput()->withErrors([
                'user_id' => 'Belum ada pengguna dengan role user yang terdaftar.',
            ]);
        }

        $currentNumber = $validated['certificate_number'];
        $createdCount = 0;
        $firstCert = null;

        foreach ($users as $user) {
            $alreadyHas = Certificate::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->exists();

            if ($alreadyHas) {
                continue;
            }

            // Cek keunikan nomor sertifikat khusus untuk kursus pelatihan ini
            while (Certificate::where('course_id', $courseId)->where('certificate_number', $currentNumber)->exists()) {
                $currentNumber = Certificate::incrementCertificateNumber($currentNumber);
            }

            do {
                $code = 'BKS-' . strtoupper(Str::random(12));
            } while (Certificate::where('certificate_code', $code)->exists());

            $cert = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'certificate_number' => $currentNumber,
                'certificate_code' => $code,
                'issued_at' => $issuedAt,
                'certificate_url' => url("/certificates/{$code}"),
                'verification_url' => url("/certificates/verify/{$code}"),
            ]);

            if (!$firstCert) {
                $firstCert = $cert;
            }
            $createdCount++;

            $currentNumber = Certificate::incrementCertificateNumber($currentNumber);
        }

        if ($createdCount === 0) {
            return redirect()->route('admin.issued-certificates.index')
                ->with('info', "Semua peserta (Role User) sudah memiliki sertifikat untuk kursus '{$course->title}'.");
        }

        $firstCert->load('course');

        ActivityLogService::log(
            action: 'certificate_created',
            entity: $firstCert,
            description: "Diterbitkan {$createdCount} sertifikat untuk seluruh pengguna role user pada kursus '{$course->title}' secara berurutan."
        );

        return redirect()->route('admin.issued-certificates.index')
            ->with('success', "Berhasil menerbitkan {$createdCount} sertifikat untuk seluruh pengguna Role User secara berurutan.");
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $issuedCertificate)
    {
        return redirect()->route('certificates.show', $issuedCertificate->id);
    }

    /**
     * Show the form for editing the certificate number.
     */
    public function edit(Certificate $issuedCertificate)
    {
        $issuedCertificate->load(['user', 'course']);

        return view('admin.issued-certificates.edit', [
            'certificate' => $issuedCertificate,
        ]);
    }

    /**
     * Update the certificate number in storage.
     */
    public function update(Request $request, Certificate $issuedCertificate)
    {
        $validated = $request->validate([
            'certificate_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('certificates', 'certificate_number')
                    ->where(fn($q) => $q->where('course_id', $issuedCertificate->course_id))
                    ->ignore($issuedCertificate->id),
            ],
            'issued_at' => ['nullable', 'date'],
        ], [
            'certificate_number.required' => 'Nomor sertifikat wajib diisi.',
            'certificate_number.unique' => 'Nomor sertifikat ini sudah digunakan pada kursus ini.',
        ]);

        $oldNumber = $issuedCertificate->certificate_number;
        $newNumber = $validated['certificate_number'];

        $updateData = [
            'certificate_number' => $newNumber,
        ];

        if (!empty($validated['issued_at'])) {
            $updateData['issued_at'] = $validated['issued_at'];
        }

        $issuedCertificate->update($updateData);

        ActivityLogService::log(
            action: 'certificate_number_updated',
            entity: $issuedCertificate,
            description: "Nomor sertifikat untuk peserta '{$issuedCertificate->user->name}' diubah dari '{$oldNumber}' menjadi '{$newNumber}'."
        );

        return redirect()->route('admin.issued-certificates.index')
            ->with('success', "Nomor sertifikat untuk '{$issuedCertificate->user->name}' berhasil diperbarui menjadi '{$newNumber}'.");
    }


    /**
     * Remove the specified certificate from storage.
     */
    public function destroy(Certificate $issuedCertificate)
    {
        $userName = $issuedCertificate->user?->name ?? 'Peserta';
        $number = $issuedCertificate->certificate_number;

        $issuedCertificate->delete();

        ActivityLogService::log(
            action: 'certificate_deleted',
            entity: $issuedCertificate,
            description: "Sertifikat nomor '{$number}' milik '{$userName}' telah dihapus."
        );

        return redirect()->route('admin.issued-certificates.index')
            ->with('success', "Sertifikat nomor '{$number}' berhasil dihapus.");
    }
}
