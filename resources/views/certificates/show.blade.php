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

            /* Kartu sertifikat pas 1 halaman */
            #certificate-print-area {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 24mm 16mm 20mm 16mm !important;
                border: none !important;
                border-radius: 0 !important;
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
            #certificate-print-area .space-y-3\.5> :not([hidden])~ :not([hidden]),
            #certificate-print-area .space-y-4> :not([hidden])~ :not([hidden]),
            #certificate-print-area .space-y-6> :not([hidden])~ :not([hidden]) {
                margin-top: 0.55rem !important;
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
            class="bg-white pt-24 pb-20 px-8 sm:pt-28 sm:pb-24 sm:px-14 md:pt-32 md:pb-24 md:px-16 shadow-2xl relative overflow-hidden text-center text-slate-900 print:shadow-none print:m-0 border border-slate-200/80">
            <!-- FRAME BINGKAI (NAVY & GOLD GEOMETRIC BORDER) -->
            <svg class="absolute inset-0 w-full h-full pointer-events-none z-0" viewBox="0 0 1024 723"
                preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Top-Left Navy Corner Block (compact to avoid overlapping center logo) -->
                <path d="M 46,49 L 320,49 L 320,71 L 71,71 L 71,320 L 46,320 Z" fill="#003579" />

                <!-- Bottom-Right Navy Corner Block (compact to avoid overlapping centered signature/NIP) -->
                <path d="M 978,673 L 700,673 L 700,651 L 953,651 L 953,379 L 978,379 Z" fill="#003579" />

                <!-- Top-Left Outer Gold Bracket -->
                <path d="M 220,31 L 31,31 L 31,240" stroke="#c59b27" stroke-width="3.5" stroke-linecap="square" />

                <!-- Bottom-Right Outer Gold Bracket -->
                <path d="M 800,691 L 993,691 L 993,480" stroke="#c59b27" stroke-width="3.5" stroke-linecap="square" />

                <!-- Top-Left Inner Gold Accent Bracket -->
                <path d="M 350,87 L 89,87 L 89,350" stroke="#c59b27" stroke-width="6.5" stroke-linecap="square" />

                <!-- Bottom-Right Inner Gold Accent Bracket -->
                <path d="M 670,636 L 935,636 L 935,346" stroke="#c59b27" stroke-width="6.5" stroke-linecap="square" />

                <!-- Connecting Outer Gold Lines -->
                <line x1="320" y1="62" x2="965" y2="62" stroke="#c59b27" stroke-width="3.5" />
                <line x1="965" y1="62" x2="965" y2="379" stroke="#c59b27" stroke-width="3.5" />
                <line x1="58" y1="320" x2="58" y2="660" stroke="#c59b27" stroke-width="3.5" />
                <line x1="58" y1="660" x2="700" y2="660" stroke="#c59b27" stroke-width="3.5" />
            </svg>

            <!-- SIMBOL BULAT BUNGA (GOLD MEDAL SEAL BADGE WITH NAVY/GOLD RIBBONS) -->
            <div
                class="absolute top-6 left-6 sm:top-9 sm:left-10 md:top-10 md:left-12 pointer-events-none z-20 w-16 sm:w-22 md:w-26 print:w-24 print:top-8 print:left-10 aspect-[140/170]">
                <svg viewBox="0 0 140 170" class="w-full h-full drop-shadow-md" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <!-- Gold Medallion Gradient (Bevel Rim) -->
                        <linearGradient id="certGoldRim" x1="20%" y1="10%" x2="80%" y2="90%">
                            <stop offset="0%" stop-color="#fae99e" />
                            <stop offset="30%" stop-color="#dfb746" />
                            <stop offset="70%" stop-color="#b88a24" />
                            <stop offset="100%" stop-color="#fdf1b6" />
                        </linearGradient>

                        <!-- Gold Face Radial Gradient (Lustrous Center) -->
                        <radialGradient id="certGoldFace" cx="38%" cy="36%" r="65%">
                            <stop offset="0%" stop-color="#fffce6" />
                            <stop offset="25%" stop-color="#f8e58c" />
                            <stop offset="55%" stop-color="#dfb748" />
                            <stop offset="85%" stop-color="#bd8a20" />
                            <stop offset="100%" stop-color="#93670c" />
                        </radialGradient>

                        <!-- Inner Groove Gradient -->
                        <linearGradient id="certInnerGroove" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#80560a" />
                            <stop offset="50%" stop-color="#d4aa3b" />
                            <stop offset="100%" stop-color="#fff8d2" />
                        </linearGradient>
                    </defs>

                    <!-- RIBBONS (Behind Medal) -->
                    <g id="certRibbons">
                        <!-- Outer Left Short Ribbon -->
                        <polygon points="50,60 22,112 34,118 40,105 58,62" fill="#002758" stroke="#c59b27"
                            stroke-width="1.8" stroke-linejoin="round" />

                        <!-- Outer Right Short Ribbon -->
                        <polygon points="90,60 118,112 106,118 100,105 82,62" fill="#002758" stroke="#c59b27"
                            stroke-width="1.8" stroke-linejoin="round" />

                        <!-- Main Left Ribbon Tail -->
                        <polygon points="52,65 30,145 48,135 66,145 68,68" fill="#002d66" stroke="#c59b27"
                            stroke-width="1.8" stroke-linejoin="round" />

                        <!-- Main Right Ribbon Tail -->
                        <polygon points="88,65 74,68 76,145 94,135 112,145" fill="#002d66" stroke="#c59b27"
                            stroke-width="1.8" stroke-linejoin="round" />
                    </g>

                    <!-- MEDAL CIRCLE (In Front) -->
                    <g id="certMedallion">
                        <!-- Outer Rim with Bevel Gradient -->
                        <circle cx="70" cy="58" r="46" fill="url(#certGoldRim)" stroke="#a1761b" stroke-width="1" />

                        <!-- Inner Groove Ring -->
                        <circle cx="70" cy="58" r="41" fill="none" stroke="url(#certInnerGroove)" stroke-width="1.5" />

                        <!-- Inner Circle Face -->
                        <circle cx="70" cy="58" r="38.5" fill="url(#certGoldFace)" />

                        <!-- Delicate Inner Decorative Rings -->
                        <circle cx="70" cy="58" r="32" fill="none" stroke="#d5ab39" stroke-width="1" stroke-opacity="0.8" />
                        <circle cx="70" cy="58" r="30" fill="none" stroke="#eed173" stroke-width="0.75"
                            stroke-opacity="0.9" />
                    </g>
                </svg>
            </div>

            <!-- Watermark background seal -->
            <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none select-none">
                <img src="{{ asset('storage/korpri.png') }}" alt="Logo KORPRI Indonesia"
                    class="w-[440px] max-w-[70%] object-contain pointer-events-none select-none">
            </div>

            <div class="relative z-10 space-y-3.5 sm:space-y-4">
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
                    <!-- <div class="text-xs text-slate-500 font-semibold mt-2">
                            {{ $certificate->user->institution ?? 'Peserta Pelatihan Mandiri' }}
                        </div> -->
                </div>

                <!-- Body -->
                <p class="text-xs sm:text-sm text-slate-700 max-w-2xl mx-auto leading-relaxed">
                    Telah berhasil menyelesaikan seluruh rangkaian pelatihan mandiri dengan materi :
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
                    <div class="relative inline-flex flex-col items-center justify-center pt-3 pb-1 min-w-[280px]">
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