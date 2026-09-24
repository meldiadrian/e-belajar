@extends('layouts.app')

@section('title', 'Sertifikat Resmi - ' . $certificate->certificate_number)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print flex items-center justify-between mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <a href="{{ route('my.certificates') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
            &larr; Kembali ke Sertifikat Saya
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('certificates.verify', $certificate->certificate_code) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200">
                Tautan Verifikasi Publik
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Certificate Layout -->
    <div class="bg-white p-8 sm:p-14 rounded-3xl border-8 border-double border-amber-600/60 shadow-2xl relative overflow-hidden text-center text-slate-900 print:border-amber-700 print:shadow-none print:m-0 print:p-8">
        <!-- Watermark background seal -->
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
            <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
        </div>

        <div class="relative z-10 space-y-6">
            <!-- Header Seal -->
            <div class="flex flex-col items-center">
                <img src="{{ asset('storage/logo.png') }}" alt="Lambang Kabupaten Bengkalis" class="w-16 h-16 object-contain mb-2 drop-shadow-sm">
                <div class="text-xs font-bold tracking-widest uppercase text-emerald-900">Pemerintah Kabupaten Bengkalis</div>
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Dinas Komunikasi, Informatika dan Statistik</div>
            </div>

            <!-- Title -->
            <div class="py-2 border-y border-amber-500/30 max-w-lg mx-auto">
                <h2 class="text-2xl sm:text-3xl font-black tracking-wider uppercase text-emerald-950 font-serif">Sertifikat Kelulusan</h2>
                <div class="text-xs font-mono font-bold text-amber-800 tracking-widest mt-1">NO: {{ $certificate->certificate_number }}</div>
            </div>

            <p class="text-xs text-slate-600 italic">Diberikan dengan penuh kehormatan kepada:</p>

            <!-- Recipient Name -->
            <div class="py-2">
                <div class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight underline decoration-amber-500 decoration-2 underline-offset-8">
                    {{ $certificate->user->name }}
                </div>
                <div class="text-xs text-slate-500 font-semibold mt-2">
                    {{ $certificate->user->institution ?? 'Peserta Pelatihan Mandiri' }}
                </div>
            </div>

            <!-- Body -->
            <p class="text-xs sm:text-sm text-slate-700 max-w-2xl mx-auto leading-relaxed">
                Telah berhasil menyelesaikan seluruh rangkaian materi pelatihan mandiri, penugasan, dan evaluasi asesmen kompetensi pada kursus:
            </p>

            <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl max-w-xl mx-auto">
                <div class="text-lg sm:text-xl font-black text-emerald-950">{{ $certificate->course->title }}</div>
                <div class="text-[11px] text-emerald-800 font-medium mt-1">Kategori: {{ $certificate->course->category->name ?? 'Kompetensi Mandiri' }} &bull; Durasi: {{ round($certificate->course->duration / 60, 1) }} Jam Pelatihan</div>
            </div>

            <!-- Signatures & Verification Stamp -->
            <div class="pt-8 grid grid-cols-2 gap-8 max-w-2xl mx-auto items-end text-xs">
                <!-- QR & Verification Code -->
                <div class="text-left space-y-1">
                    <div class="text-[10px] uppercase font-bold text-slate-400">Verifikasi Dokumen Elektronik</div>
                    <div class="font-mono font-bold text-emerald-800 text-xs">{{ $certificate->certificate_code }}</div>
                    <div class="text-[10px] text-slate-500">Pindai / Periksa di:</div>
                    <a href="{{ $certificate->verification_url }}" target="_blank" class="text-[10px] text-emerald-700 underline block font-mono truncate max-w-xs">
                        {{ $certificate->verification_url }}
                    </a>
                </div>

                <!-- Signature -->
                <div class="text-right space-y-1">
                    <div class="text-slate-600">Bengkalis, {{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                    <div class="text-[11px] font-bold text-slate-800">Pemerintah Kabupaten Bengkalis</div>
                    <div class="h-14 flex items-center justify-end">
                        <span class="px-3 py-1 bg-emerald-50 border border-emerald-300 rounded-lg text-[10px] font-bold text-emerald-800 uppercase tracking-widest">
                            Tersertifikasi Digital (BSRE/E-Belajar)
                        </span>
                    </div>
                    <div class="font-bold text-slate-900 border-t border-slate-300 pt-1 inline-block">Badan Penyelenggara E-Belajar</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
