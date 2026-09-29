@extends('layouts.app')

@section('title', 'Sertifikat Resmi - ' . $certificate->certificate_number)

@push('styles')
    <style>
        @media print {

            /* Hapus header dan footer teks bawaan browser (URL, tanggal, halaman) */
            @page {
                size: landscape;
                margin: 0;
            }

            /* Sembunyikan semua elemen selain sertifikat */
            body>*:not(main),
            nav,
            footer,
            .no-print,
            .certificate-page-container>*:not(#certificate-print-area) {
                display: none !important;
            }

            html,
            body {
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                background: #ffffff !important;
                background-color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                background: #ffffff !important;
            }

            /* Wadah halaman cetak tepat 1 halaman landscape */
            .certificate-page-container {
                width: 100% !important;
                height: 100vh !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 6mm 10mm !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                background: #ffffff !important;
            }

            /* Kartu sertifikat pas 1 halaman dengan border ganda rapi */
            #certificate-print-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 20px 28px !important;
                border: 8px double #dc2626 !important;
                border-radius: 1.5rem !important;
                box-shadow: none !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
                break-inside: avoid !important;
                break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Jarak antar bagian disesuaikan presisi agar tidak tumpah ke halaman 2 */
            #certificate-print-area .space-y-6> :not([hidden])~ :not([hidden]) {
                margin-top: 0.75rem !important;
            }

            #certificate-print-area h2 {
                font-size: 1.6rem !important;
                line-height: 1.2 !important;
            }

            #certificate-print-area .text-2xl,
            #certificate-print-area .sm\:text-4xl {
                font-size: 1.7rem !important;
                line-height: 1.2 !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-8 certificate-page-container">
        <!-- Top Action Bar (hidden on print) -->
        <div
            class="no-print flex items-center justify-between mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <a href="{{ route('my.certificates') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Kembali ke Sertifikat Saya
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('certificates.verify', $certificate->certificate_code) }}" target="_blank"
                    class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Tautan Verifikasi Publik
                </a>
                <button onclick="window.print()"
                    class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        <!-- Official Printable Certificate Layout -->
        <div id="certificate-print-area"
            class="bg-white p-8 sm:p-14 rounded-3xl border-8 border-double border-red-600 shadow-2xl relative overflow-hidden text-center text-slate-900 print:border-red-600 print:shadow-none print:m-0 print:p-8">
            <!-- Watermark background seal -->
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
                </svg>
            </div>

            <div class="relative z-10 space-y-6">
                <!-- Header Seal -->
                <div class="flex flex-col items-center">
                    <img src="{{ asset('storage/logo.png') }}" alt="Lambang Kabupaten Bengkalis"
                        class="w-16 h-16 object-contain mb-2 drop-shadow-sm">
                    <div class="text-xs font-bold tracking-widest uppercase text-emerald-900">Pemerintah Kabupaten Bengkalis
                    </div>
                    <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider"> Badan Kepegawaian,
                        Pendidikan dan Pelatihan</div>
                </div>

                <!-- Title -->
                <div class="py-2 border-y border-amber-500/30 max-w-lg mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-wider uppercase text-emerald-950 font-serif">
                        Sertifikat Kelulusan</h2>
                    <div class="text-xs font-mono font-bold text-amber-800 tracking-widest mt-1">NO:
                        {{ $certificate->certificate_number }}
                    </div>
                </div>

                <p class="text-xs text-slate-600 italic">Diberikan dengan penuh kehormatan kepada:</p>

                <!-- Recipient Name -->
                <div class="py-2">
                    <div
                        class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight underline decoration-amber-500 decoration-2 underline-offset-8">
                        {{ $certificate->user->name }}
                    </div>
                    <div class="text-xs text-slate-500 font-semibold mt-2">
                        {{ $certificate->user->institution ?? 'Peserta Pelatihan Mandiri' }}
                    </div>
                </div>

                <!-- Body -->
                <p class="text-xs sm:text-sm text-slate-700 max-w-2xl mx-auto leading-relaxed">
                    Telah berhasil menyelesaikan seluruh rangkaian materi pelatihan mandiri, penugasan, dan evaluasi asesmen
                    kompetensi pada kursus:
                </p>

                <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl max-w-xl mx-auto">
                    <div class="text-lg sm:text-xl font-black text-emerald-950">{{ $certificate->course->title }}</div>
                    <div class="text-[11px] text-emerald-800 font-medium mt-1">Kategori:
                        {{ $certificate->course->category->name ?? 'Kompetensi Mandiri' }} &bull; Durasi:
                        {{ round($certificate->course->duration / 60, 1) }} Jam Pelatihan
                    </div>
                </div>

                <!-- Signer & Official Stamp Section -->
                <div class="pt-4 text-center space-y-1">
                    <div class="text-xs text-slate-700">
                        Bengkalis,
                        {{ $certificate->issued_at ? $certificate->issued_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                    </div>

                    <div class="text-xs font-bold text-slate-900">
                        {{ $signer->jabatan ?? 'Kepala Dinas Komunikasi, Informatika dan Statistik' }}
                    </div>

                    <div class="text-xs font-bold text-slate-900">
                        {{ $signer->instansi ?? 'Kabupaten Bengkalis' }}
                    </div>

                    <!-- Container for Signer Name, NIP, and Overlapping Stamp/Signature -->
                    <div class="relative inline-flex flex-col items-center justify-center pt-8 pb-2 min-w-[280px]">
                        @if(!empty($signer?->signature_image))
                            <img src="{{ asset('storage/' . $signer->signature_image) }}" alt="Tanda Tangan & Stempel"
                                class="absolute -left-12 -top-4 w-36 h-28 object-contain pointer-events-none select-none z-0 mix-blend-multiply opacity-95">
                        @endif

                        <div class="relative z-10 text-center">
                            <div
                                class="font-bold text-sm sm:text-base text-slate-900 tracking-wide underline decoration-slate-900 decoration-1 underline-offset-2">
                                {{ $signer->name ?? 'AGUS SOFYAN, S.STP.,MPA' }}
                            </div>
                            <div class="text-xs text-slate-800 font-mono mt-0.5">
                                NIP.{{ $signer->nip ?? '197908161998021001' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection