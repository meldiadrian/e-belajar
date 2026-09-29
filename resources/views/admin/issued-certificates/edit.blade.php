@extends('layouts.admin')

@section('title', 'Ubah Nomor Sertifikat - ' . $certificate->certificate_number)
@section('page_title', 'Ubah Nomor Sertifikat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-lg font-black text-slate-900">Ubah Nomor Sertifikat</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui nomor registrasi sertifikat resmi untuk peserta pelatihan.</p>
            </div>
            <a href="{{ route('certificates.show', $certificate->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4D52B4] hover:text-[#4E9CE8] bg-[#4E9CE8]/10 px-3 py-1.5 rounded-lg border border-[#4E9CE8]/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat Sertifikat Asli</span>
            </a>
        </div>

        <!-- Info Box: Recipient & Course Details -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Nama Peserta Penerima:</span>
                <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $certificate->user?->name ?? 'User Tidak Diketahui' }}</span>
                <span class="text-slate-500 font-mono">{{ $certificate->user?->nip ? 'NIP. ' . $certificate->user->nip : $certificate->user?->email }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Kursus Pelatihan:</span>
                <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $certificate->course?->title ?? '-' }}</span>
                <span class="text-slate-500">{{ $certificate->course?->category?->name ?? 'Kompetensi Mandiri' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Kode Unik Verifikasi:</span>
                <span class="font-mono font-bold text-[#4D52B4] text-xs block mt-0.5">{{ $certificate->certificate_code }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Tanggal Diterbitkan Saat Ini:</span>
                <span class="font-semibold text-slate-700 block mt-0.5">{{ $certificate->issued_at ? $certificate->issued_at->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
            </div>
        </div>

        <form action="{{ route('admin.issued-certificates.update', $certificate->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Certificate Number -->
            <div>
                <label for="certificate_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nomor Registrasi Sertifikat <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="certificate_number" 
                       id="certificate_number" 
                       value="{{ old('certificate_number', $certificate->certificate_number) }}" 
                       required 
                       placeholder="Contoh: CERT/BKS/2026/09/00001" 
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden font-mono tracking-wider font-bold text-slate-900">
                <p class="text-[11px] text-slate-400 mt-1">Nomor ini tampil langsung di bagian atas lembar sertifikat resmi (di bawah judul SERTIFIKAT KELULUSAN).</p>
                @error('certificate_number')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Issued Date -->
            <div>
                <label for="issued_at" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Tanggal Terbit Sertifikat
                </label>
                <input type="date" 
                       name="issued_at" 
                       id="issued_at" 
                       value="{{ old('issued_at', $certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : '') }}" 
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden font-mono">
                <p class="text-[11px] text-slate-400 mt-1">Tanggal ini tertera pada tanda tangan pejabat: "Bengkalis, [Tanggal]".</p>
                @error('issued_at')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit & Cancel -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.issued-certificates.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#4D52B4] hover:bg-[#4E9CE8] text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Nomor Sertifikat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
