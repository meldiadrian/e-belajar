<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateSettingController extends Controller
{
    /**
     * Display a listing of certificate signers.
     */
    public function index()
    {
        $signers = CertificateSetting::latest()->paginate(10);
        $activeSigner = CertificateSetting::getActive();

        return view('admin.certificates.index', compact('signers', 'activeSigner'));
    }

    /**
     * Show the form for creating a new certificate signer.
     */
    public function create()
    {
        return view('admin.certificates.create');
    }

    /**
     * Store a newly created certificate signer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'signature_image' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama penandatangan wajib diisi.',
            'nip.required' => 'NIP penandatangan wajib diisi.',
            'signature_image.image' => 'File tanda tangan harus berupa gambar.',
            'signature_image.mimes' => 'Format gambar yang diperbolehkan: PNG, JPG, JPEG, WEBP.',
            'signature_image.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature_image')) {
            $signaturePath = $request->file('signature_image')->store('signatures', 'public');
        }

        $isActive = $request->boolean('is_active', true);

        if ($isActive) {
            CertificateSetting::where('is_active', true)->update(['is_active' => false]);
        }

        $signer = CertificateSetting::create([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'jabatan' => $validated['jabatan'] ?? 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => $validated['instansi'] ?? 'Kabupaten Bengkalis',
            'signature_image' => $signaturePath,
            'is_active' => $isActive,
        ]);

        ActivityLogService::log(
            action: 'certificate_signer_created',
            entity: $signer,
            description: "Penandatangan sertifikat '{$signer->name}' berhasil ditambahkan."
        );

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Data penandatangan sertifikat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified certificate signer.
     */
    public function edit(CertificateSetting $certificate)
    {
        return view('admin.certificates.edit', ['signer' => $certificate]);
    }

    /**
     * Update the specified certificate signer in storage.
     */
    public function update(Request $request, CertificateSetting $certificate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'signature_image' => ['nullable', 'file', 'image', 'mimes:png,jpg,jpeg,webp', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama penandatangan wajib diisi.',
            'nip.required' => 'NIP penandatangan wajib diisi.',
            'signature_image.image' => 'File tanda tangan harus berupa gambar.',
            'signature_image.mimes' => 'Format gambar yang diperbolehkan: PNG, JPG, JPEG, WEBP.',
            'signature_image.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $signaturePath = $certificate->signature_image;
        if ($request->hasFile('signature_image')) {
            // Delete old custom image if exists
            if ($certificate->signature_image && !str_contains($certificate->signature_image, 'sample-ttd') && Storage::disk('public')->exists($certificate->signature_image)) {
                Storage::disk('public')->delete($certificate->signature_image);
            }
            $signaturePath = $request->file('signature_image')->store('signatures', 'public');
        }

        $isActive = $request->boolean('is_active', $certificate->is_active);

        if ($isActive && !$certificate->is_active) {
            CertificateSetting::where('id', '!=', $certificate->id)->update(['is_active' => false]);
        }

        $certificate->update([
            'name' => $validated['name'],
            'nip' => $validated['nip'],
            'jabatan' => $validated['jabatan'] ?? 'Kepala Dinas Komunikasi, Informatika dan Statistik',
            'instansi' => $validated['instansi'] ?? 'Kabupaten Bengkalis',
            'signature_image' => $signaturePath,
            'is_active' => $isActive,
        ]);

        ActivityLogService::log(
            action: 'certificate_signer_updated',
            entity: $certificate,
            description: "Data penandatangan sertifikat '{$certificate->name}' diperbarui."
        );

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Data penandatangan sertifikat berhasil diperbarui.');
    }

    /**
     * Remove the specified certificate signer from storage.
     */
    public function destroy(CertificateSetting $certificate)
    {
        $wasActive = $certificate->is_active;

        if ($certificate->signature_image && !str_contains($certificate->signature_image, 'sample-ttd') && Storage::disk('public')->exists($certificate->signature_image)) {
            Storage::disk('public')->delete($certificate->signature_image);
        }

        $certificate->delete();

        // If active signer was deleted, set the latest remaining signer as active
        if ($wasActive) {
            $next = CertificateSetting::latest()->first();
            if ($next) {
                $next->update(['is_active' => true]);
            }
        }

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Data penandatangan sertifikat berhasil dihapus.');
    }

    /**
     * Set a signer as active.
     */
    public function setActive(CertificateSetting $certificate)
    {
        CertificateSetting::where('id', '!=', $certificate->id)->update(['is_active' => false]);
        $certificate->update(['is_active' => true]);

        return redirect()->route('admin.certificates.index')
            ->with('success', "Penandatangan '{$certificate->name}' telah diaktifkan untuk sertifikat.");
    }
}
