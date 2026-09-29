<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IssuedCertificateController extends Controller
{
    /**
     * Display a listing of issued certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with(['user', 'course'])->latest('issued_at');

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
                Rule::unique('certificates', 'certificate_number')->ignore($issuedCertificate->id),
            ],
            'issued_at' => ['nullable', 'date'],
        ], [
            'certificate_number.required' => 'Nomor sertifikat wajib diisi.',
            'certificate_number.unique' => 'Nomor sertifikat ini sudah digunakan oleh peserta lain.',
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
