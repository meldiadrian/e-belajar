@extends('layouts.app')

@section('title', 'Portal Pembelajaran ASN - E-Belajar Kabupaten Bengkalis')

@push('styles')
    <style>
        /* Custom Micro-animations & Design Tokens (INAgov & DTS Inspired) */
        @keyframes pulseSlow {

            0%,
            100% {
                opacity: 0.9;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.03);
            }
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-8px) rotate(2deg);
            }
        }

        .animate-float {
            animation: floatOrb 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulseSlow 4s ease-in-out infinite;
        }

        /* E-Belajar New Color Palette Mesh Gradient */
        .govtech-hero {
            background-color: #2F3375;
            /* Darker shade of #4D52B4 for contrast */
            background-image:
                radial-gradient(at 0% 0%, #4D52B4 0px, transparent 60%),
                radial-gradient(at 100% 0%, #4E9CE8 0px, transparent 60%),
                radial-gradient(at 50% 100%, #70D6C5 0px, transparent 60%),
                radial-gradient(at 100% 100%, #2F3375 0px, transparent 60%);
        }

        .govtech-card {
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .card-hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(4, 120, 87, 0.12), 0 8px 10px -6px rgba(4, 120, 87, 0.08);
        }

        details summary::-webkit-details-marker {
            display: none;
        }

        details[open] summary .faq-chevron {
            transform: rotate(180deg);
        }
    </style>

@endpush

@section('content')
    <!-- ==================== HERO SECTION (INAgov / DTS STYLE) ==================== -->
    <section class="relative overflow-hidden govtech-hero text-white pt-10 pb-20 lg:pt-14 lg:pb-28">
        <!-- Ambient Cyber Orbs -->
        <div
            class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#4E9CE8]/20 blur-3xl pointer-events-none animate-pulse-slow">
        </div>
        <div class="absolute top-1/2 -left-32 w-80 h-80 rounded-full bg-[#70D6C5]/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 right-1/4 w-80 h-80 rounded-full bg-[#CAE5BC]/15 blur-3xl pointer-events-none">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Column: Copy, Search & Actions -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Institutional Breadcrumb Badge -->
                    <div
                        class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-[#4D52B4]/60 border border-[#4E9CE8]/40 text-[#CAE5BC] text-xs sm:text-sm font-semibold backdrop-blur-md shadow-inner">
                        <span class="relative flex h-2.5 w-2.5">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#70D6C5] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#70D6C5]"></span>
                        </span>
                        <span>Platform Pengembangan Kompetensi ASN • Kab. Bengkalis</span>
                    </div>

                    <!-- Main Heading -->
                    <h1 class="text-3xl sm:text-5xl lg:text-5.5xl font-extrabold tracking-tight text-white leading-tight">
                        Akselerasi Kompetensi & Transformasi Digital ASN <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-[#70D6C5] via-[#4E9CE8] to-white">Bengkalis
                            Bermasa</span>
                    </h1>

                    <!-- Lead Description -->
                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                        Ekosistem pembelajaran mandiri (LMS) dan Corporate University bagi seluruh ASN (PNS & PPPK) dan
                        aparatur Pemerintah Kabupaten Bengkalis. Kurikulum terstruktur, pemenuhan minimal 20 JP per tahun,
                        asesmen kuis mandiri, dan e-sertifikat terverifikasi resmi.
                    </p>

                    <!-- DTS / INAgov Integrated Search Component -->
                    <!-- <div
                                                                                                            class="bg-slate-900/80 p-2 sm:p-2.5 rounded-2xl border border-slate-700/80 shadow-2xl backdrop-blur-md max-w-xl"> -->
                    <!-- <form action="{{ route('courses.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                                                                                                                        <div class="relative flex-1">
                                                                                                                            <input type="text" name="search"
                                                                                                                                placeholder="Cari kursus ASN, SPBE, keuangan, manajerial..."
                                                                                                                                class="w-full px-4 py-3 pl-10 rounded-xl bg-slate-950/70 border border-slate-700 focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20 text-white placeholder-slate-400 text-xs sm:text-sm font-medium transition-all">
                                                                                                                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none"
                                                                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                                                                                            </svg>
                                                                                                                        </div>
                                                                                                                        <button type="submit"
                                                                                                                            class="px-5 py-3 rounded-xl font-bold text-white bg-gradient-to-r from-[#4E9CE8] to-[#4D52B4] hover:from-[#70D6C5] hover:to-[#4E9CE8] shadow-md shadow-[#4D52B4]/30 text-xs sm:text-sm flex items-center justify-center gap-2 transition-all">
                                                                                                                            <span>Cari Modul</span>
                                                                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                                                                            </svg>
                                                                                                                        </button>
                                                                                                                    </form> -->

                    <!-- Quick Category Pills -->
                    <!-- <div class="flex flex-wrap items-center gap-1.5 pt-2.5 px-1 text-[11px] text-slate-400">
                                                                                                                    <span class="font-semibold text-slate-400">Topik Populer:</span>
                                                                                                                    <a href="{{ route('courses.index', ['search' => 'SPBE']) }}"
                                                                                                                        class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 transition-colors">SPBE</a>
                                                                                                                    <a href="{{ route('courses.index', ['search' => 'Keuangan']) }}"
                                                                                                                        class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 transition-colors">Keuangan
                                                                                                                        Daerah</a>
                                                                                                                    <a href="{{ route('courses.index', ['search' => 'Administrasi']) }}"
                                                                                                                        class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 transition-colors">Administrasi</a>
                                                                                                                    <a href="{{ route('courses.index') }}"
                                                                                                                        class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-emerald-900/60 hover:text-emerald-300 transition-colors">Semua
                                                                                                                        Modul &rarr;</a>
                                                                                                                </div> -->
                    <!-- </div> -->

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3.5 pt-1">
                        <a href="{{ route('login') }}"
                            class="px-5 py-3 rounded-xl font-bold text-white bg-[#4D52B4] hover:bg-[#4E9CE8] shadow-lg shadow-[#4D52B4]/40 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Lihat Katalog Pelatihan</span>
                        </a>

                        <button type="button" onclick="openGuideModal()"
                            class="px-4 py-3 rounded-xl font-semibold text-slate-200 bg-slate-800/80 hover:bg-slate-700 border border-slate-700 hover:border-emerald-500/50 backdrop-blur-md transition-all flex items-center gap-2 text-sm">
                            <div
                                class="w-6 h-6 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 fill-current ml-0.5" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </div>
                            <span>Panduan LMS ASN</span>
                        </button>

                        @guest
                            <a href="{{ route('register') }}"
                                class="px-4 py-3 rounded-xl font-semibold text-amber-300 hover:text-amber-200 hover:underline transition-colors flex items-center gap-1.5 text-sm">
                                <span>Aktivasi Akun ASN</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endguest
                    </div>
                </div>

                <!-- Right Column: INAgov Digital Academy Preview Card -->
                <div class="lg:col-span-5">
                    <div
                        class="relative govtech-card rounded-3xl p-6 sm:p-7 shadow-2xl overflow-hidden border border-emerald-500/20">
                        <!-- Top Gradient Accent -->
                        <div
                            class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-amber-400">
                        </div>

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                                    class="w-9 h-9 object-contain">
                                <div>
                                    <div class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Government
                                        Digital Academy</div>
                                    <div class="text-xs font-bold text-white">E-Belajar ASN Kab. Bengkalis</div>
                                </div>
                            </div>
                            <!-- <span
                                                                                                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                                                                                                20 JP / Tahun
                                                                                                                            </span> -->
                        </div>

                        <!-- 20 JP Target Indicator Card -->
                        <div
                            class="rounded-2xl bg-gradient-to-br from-[#4D52B4]/90 via-[#2F3375] to-slate-900 p-4 sm:p-5 border border-slate-700/80 mb-4 shadow-xl">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 mb-2">
                                <span class="flex items-center gap-1.5 text-amber-400 font-bold">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Target Pengembangan Kompetensi ASN
                                </span>
                                <span class="text-emerald-400 font-bold">UU No. 20/2023</span>
                            </div>
                            <p class="text-xs text-slate-300 leading-relaxed mb-3">
                                Setiap ASN memiliki hak dan kewajiban pengembangan kompetensi minimal <strong>20 Jam
                                    Pelajaran (JP)</strong> per tahun. Modul di E-Belajar dikonversi otomatis ke bukti
                                dukung SKP.
                            </p>
                            <!-- Mini Progress Visual -->
                            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden mb-1">
                                <div
                                    class="bg-gradient-to-r from-[#4E9CE8] to-[#70D6C5] h-2 rounded-full w-4/5 animate-pulse">
                                </div>
                            </div>
                            <div class="flex justify-between text-[10px] text-slate-400">
                                <span>Microlearning Asinkronus</span>
                                <span class="text-emerald-300 font-semibold">Tersertifikasi</span>
                            </div>
                        </div>

                        <!-- 3 Core Feature Items -->
                        <div class="space-y-2 text-xs">
                            <div
                                class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div
                                    class="w-5 h-5 rounded-md bg-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0 font-bold text-[11px]">
                                    ✓
                                </div>
                                <span class="text-slate-200">Kurikulum SPBE, SAKIP, dan Manajemen Kinerja</span>
                            </div>
                            <div
                                class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div
                                    class="w-5 h-5 rounded-md bg-teal-500/20 text-teal-400 flex items-center justify-center flex-shrink-0 font-bold text-[11px]">
                                    ✓
                                </div>
                                <span class="text-slate-200">Asesmen Kuis Mandiri dengan Batas Kelulusan Objektif</span>
                            </div>
                            <div
                                class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                <div
                                    class="w-5 h-5 rounded-md bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0 font-bold text-[11px]">
                                    ✓
                                </div>
                                <span class="text-slate-200">E-Sertifikat Sah dengan QR Code Verifikasi BKPPD</span>
                            </div>
                        </div>

                        <!-- Quick Certificate Verifier trigger -->
                        <div class="mt-4 pt-3.5 border-t border-slate-700/60 text-center">
                            <a href="#verifikasi-section"
                                class="text-xs text-amber-300 hover:text-amber-200 font-semibold inline-flex items-center gap-1.5 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Verifikasi Keaslian Sertifikat ASN &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave SVG Divider Bottom -->
        <div class="absolute bottom-0 left-0 right-0 w-full overflow-hidden leading-none pointer-events-none">
            <svg class="relative block w-full h-8 sm:h-12 text-slate-900" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0 C150,90 350,-40 500,45 C650,130 900,10 1200,60 L1200,120 L0,120 Z" fill="currentColor"></path>
            </svg>
        </div>
    </section>

    <!-- ==================== CORE VALUES BerAKHLAK & SMART ASN TICKER ==================== -->
    <div class="bg-slate-900 text-slate-300 border-b border-slate-800 py-3.5 px-4 overflow-x-auto no-scrollbar">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 text-xs font-medium min-w-[700px]">
            <div
                class="flex items-center gap-2 text-amber-400 font-bold uppercase tracking-wider text-[11px] flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Core Values ASN:</span>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Berorientasi
                    Pelayanan</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Akuntabel</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Kompeten</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Harmonis</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Loyal</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Adaptif</span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 border border-slate-700">Kolaboratif</span>
            </div>
            <div class="text-[11px] text-slate-400 font-semibold flex-shrink-0">
                🇮🇩 Smart ASN Bengkalis BERMASA
            </div>
        </div>
    </div>

    <!-- ==================== JALUR AKADEMI KOMPETENSI ASN (DTS ACADEMY STYLE) ==================== -->
    <section class="bg-slate-100/70 border-y border-slate-200/80 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
                <div>
                    <div
                        class="inline-flex items-center gap-2 text-xs font-bold text-[#4D52B4] bg-[#4E9CE8]/15 border border-[#4E9CE8]/30 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                        <span>Pilar Akademi Pembelajaran ASN</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Klaster Kompetensi Aparatur Kabupaten Bengkalis
                    </h2>
                    <p class="text-sm text-slate-600 mt-1">
                        Pilih rumpun keahlian sesuai dengan Rencana Kinerja Pegawai (SKP) dan tugas pokok fungsi OPD Anda
                    </p>
                </div>
                <a href="{{ route('login') }}"
                    class="text-sm font-bold text-[#4D52B4] hover:text-[#4E9CE8] flex items-center gap-1.5 transition-colors group">
                    <span>Lihat Semua Modul Pelatihan</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5">
                @php
                    $clusterPresets = [
                        'Teknologi' => [
                            'title' => 'Transformasi Digital & SPBE',
                            'badge' => 'Digital Academy',
                            'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                            'bg' => 'bg-[#70D6C5]/20 text-[#2F3375] border-[#70D6C5]/40',
                            'badgeClass' => 'bg-[#70D6C5]/30 text-[#2F3375]'
                        ],
                        'Administrasi' => [
                            'title' => 'Tata Kelola Pemerintahan',
                            'badge' => 'Gov Governance',
                            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            'bg' => 'bg-teal-50 text-teal-700 border-teal-200',
                            'badgeClass' => 'bg-teal-100 text-teal-800'
                        ],
                        'Keuangan' => [
                            'title' => 'Keuangan Daerah & Desa',
                            'badge' => 'Public Finance',
                            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                            'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'badgeClass' => 'bg-amber-100 text-amber-800'
                        ],
                        'Pendidikan' => [
                            'title' => 'Pendidik & Kependidikan',
                            'badge' => 'Teacher Academy',
                            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'badgeClass' => 'bg-blue-100 text-blue-800'
                        ],
                        'Kesehatan' => [
                            'title' => 'Layanan Mutu Kesehatan',
                            'badge' => 'Health Services',
                            'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                            'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'badgeClass' => 'bg-rose-100 text-rose-800'
                        ],
                    ];
                @endphp

                @forelse($categories as $category)
                    @php
                        $preset = $clusterPresets[$category->name] ?? [
                            'title' => $category->name,
                            'badge' => 'Akademi ASN',
                            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                            'bg' => 'bg-[#4E9CE8]/15 text-[#4D52B4] border-[#4E9CE8]/30',
                            'badgeClass' => 'bg-[#4E9CE8]/20 text-[#4D52B4]'
                        ];
                    @endphp
                    <a href="{{ route('login', ['category' => $category->slug]) }}"
                        class="group bg-white p-5 rounded-2xl border border-slate-200/90 hover:border-[#4E9CE8] shadow-xs hover:shadow-lg card-hover-lift flex flex-col justify-between transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 rounded-xl {{ $preset['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $preset['icon'] }}" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $preset['badgeClass'] }}">
                                    {{ $preset['badge'] }}
                                </span>
                            </div>
                            <h3
                                class="font-bold text-slate-800 text-sm group-hover:text-[#4D52B4] transition-colors leading-snug mb-1">
                                {{ $preset['title'] }}
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2">
                                Rumpun materi {{ strtolower($category->name) }} untuk ASN Pemkab Bengkalis.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="font-bold text-[#4D52B4]">{{ $category->courses_count }} Modul Kursus</span>
                            <span class="text-slate-400 group-hover:text-[#4E9CE8] transition-colors">&rarr;</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full bg-white rounded-2xl p-8 text-center text-slate-400 border border-slate-200">
                        Belum ada klaster akademi aktif saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- ==================== KATALOG KURSUS UNGGULAN ASN (FEATURED COURSES) ==================== -->
    <!-- <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20"> -->
    <!-- <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-10">
                                                                            <div>
                                                                                <div
                                                                                    class="inline-flex items-center gap-2 text-xs font-bold text-[#4D52B4] bg-[#4E9CE8]/15 border border-[#4E9CE8]/30 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                                                                                    <span>Program Pelatihan Prioritas</span>
                                                                                </div>
                                                                                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                                                                                    Modul Kompetensi Terpopuler ASN
                                                                                </h2>
                                                                                <p class="text-sm text-slate-600 mt-1">
                                                                                    Disusun bersama narasumber ahli untuk menjawab tantangan tata kelola pemerintahan era digital
                                                                                </p>
                                                                            </div>
                                                                            <a href="{{ route('courses.index') }}"
                                                                                class="text-sm font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5 transition-colors group">
                                                                                <span>Telusuri Semua Kursus</span>
                                                                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                                </svg>
                                                                            </a>
                                                                        </div> -->

    <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                                                                        @forelse($featuredCourses as $course)
                                                                            <div
                                                                                class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl card-hover-lift overflow-hidden flex flex-col group transition-all"> -->
    <!-- Thumbnail Header -->
    <!-- <div
                                                                                    class="relative h-48 bg-gradient-to-br from-emerald-950 via-slate-900 to-teal-950 flex items-center justify-center text-white overflow-hidden">
                                                                                    @if($course->thumbnail)
                                                                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                                                                    @else
                                                                                        <div class="text-center p-6">
                                                                                            <div
                                                                                                class="w-14 h-14 mx-auto mb-2 rounded-2xl bg-white/10 flex items-center justify-center text-amber-400 backdrop-blur-xs group-hover:scale-110 transition-transform">
                                                                                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                                                                                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z" />
                                                                                                </svg>
                                                                                            </div>
                                                                                            <span class="text-xs font-semibold text-emerald-300 uppercase tracking-wider">
                                                                                                {{ $course->category->name ?? 'Kompetensi ASN' }}
                                                                                            </span>
                                                                                        </div>
                                                                                    @endif -->

    <!-- Category Pill (Top-Left) -->
    <!-- <div
                                                                                        class="absolute top-3 left-3 bg-slate-900/85 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-bold text-emerald-300 border border-emerald-500/30">
                                                                                        {{ $course->category->name ?? 'Umum' }}
                                                                                    </div> -->

    <!-- 20 JP / Certificate Seal (Top-Right) -->
    <!-- @if($course->certificate_enabled)
                                                                                        <div
                                                                                            class="absolute top-3 right-3 bg-slate-900/90 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-bold text-amber-400 flex items-center gap-1 border border-amber-400/40 shadow-sm">
                                                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                                                                <path fill-rule="evenodd"
                                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                                    clip-rule="evenodd" />
                                                                                            </svg>
                                                                                            <span>Sertifikat Diakui</span>f
                                                                                        </div>
                                                                                    @endif
                                                                                </div> -->

    <!-- Body Content -->
    <!-- <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                                                                    <div>
                                                                                        <div class="flex items-center gap-2 mb-2">
                                                                                            <span class="text-[11px] font-bold text-[#4D52B4] bg-[#4E9CE8]/15 px-2 py-0.5 rounded">
                                                                                                Government Transformation
                                                                                            </span>
                                                                                        </div>
                                                                                        <h3
                                                                                            class="text-base sm:text-lg font-bold text-slate-900 leading-snug group-hover:text-[#4E9CE8] transition-colors line-clamp-2">
                                                                                            <a href="{{ route('courses.show', $course->slug ?? $course->id) }}">
                                                                                                {{ $course->title }}
                                                                                            </a>
                                                                                        </h3>
                                                                                        <p class="text-xs sm:text-sm text-slate-500 mt-2 line-clamp-2 leading-relaxed">
                                                                                            {{ Str::limit($course->description, 110) }}
                                                                                        </p>
                                                                                    </div> -->

    <!-- Metadata Row (JP, Lessons, Enrolled) -->
    <!-- <div class="space-y-3 pt-3 border-t border-slate-100">
                                                                                        <div class="flex items-center justify-between text-xs text-slate-500">
                                                                                            <div class="flex items-center gap-1.5" title="Estimasi Durasi Belajar">
                                                                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                                </svg>
                                                                                                <span
                                                                                                    class="font-semibold text-slate-700">{{ $course->duration > 0 ? $course->duration . ' Menit' : 'Fleksibel Mandiri' }}</span>
                                                                                            </div>
                                                                                            <div class="flex items-center gap-1.5" title="Materi Pembelajaran">
                                                                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                                                                </svg>
                                                                                                <span>{{ $course->lessons_count }} Modul</span>
                                                                                            </div>
                                                                                            <div class="flex items-center gap-1.5" title="Partisipan ASN">
                                                                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                                                </svg>
                                                                                                <span>{{ $course->enrollments_count }} ASN</span>
                                                                                            </div>
                                                                                        </div> -->

    <!-- Button Action -->
    <!-- @auth
                                                                                            <a href="{{ route('courses.show', $course->slug ?? $course->id) }}"
                                                                                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-center text-white bg-[#4D52B4] hover:bg-[#4E9CE8] shadow-md shadow-[#4D52B4]/30 transition-all flex items-center justify-center gap-1.5 group-hover:bg-[#4E9CE8]">
                                                                                                <span>Mulai Belajar &rarr;</span>
                                                                                            </a>
                                                                                        @else
                                                                                            <a href="{{ route('courses.show', $course->slug ?? $course->id) }}"
                                                                                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-center text-[#4D52B4] bg-[#4E9CE8]/15 hover:bg-[#4D52B4] hover:text-white transition-all flex items-center justify-center gap-1.5 group-hover:bg-[#4D52B4] group-hover:text-white">
                                                                                                <span>Lihat Detail Modul &rarr;</span>
                                                                                            </a>
                                                                                        @endauth
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-200">
                                                                                <div
                                                                                    class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-[#4E9CE8]/15 text-[#4D52B4] flex items-center justify-center">
                                                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                                                    </svg>
                                                                                </div>
                                                                                <h3 class="text-base font-bold text-slate-800">Modul Pelatihan Sedang Disiapkan</h3>
                                                                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Kurikulum baru sedang dalam proses kurasi dan
                                                                                    akreditasi oleh Diskominfotik Kabupaten Bengkalis.</p>
                                                                            </div>
                                                                        @endforelse
                                                                    </div>
                                                                </section> -->

    <!-- ==================== KEUNGGULAN / VALUE PROPOSITION ASN ==================== -->
    <section class="bg-white border-t border-slate-200/80 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span
                    class="inline-block text-xs font-bold text-[#4D52B4] bg-[#4E9CE8]/15 border border-[#4E9CE8]/30 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    Standar LMS Pemerintah
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Mengapa E-Belajar ASN Bengkalis?
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3">
                    Inovasi Corporate University daerah untuk mempermudah seluruh aparatur mencapai standar kompetensi
                    nasional secara efektif dan efisien.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div
                    class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/80 hover:border-emerald-400 hover:bg-white shadow-xs hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-emerald-700 transition-colors">
                        Pemenuhan 20 JP Mandiri
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Membantu setiap ASN menuntaskan kewajiban pengembangan kompetensi tahunan tanpa terikat tempat
                        dan
                        jam kerja kantor.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/80 hover:border-teal-400 hover:bg-white shadow-xs hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-teal-700 transition-colors">
                        Kurikulum Relevan & Terkini
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Materi spesifik berfokus pada penguatan SPBE, transformasi digital layanan, akuntabilitas SAKIP,
                        dan
                        regulasi daerah.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/80 hover:border-blue-400 hover:bg-white shadow-xs hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-blue-700 transition-colors">
                        Evaluasi & Kuis Terstandar
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem asesmen kuis objektif dengan passing grade kelulusan untuk menjamin pemahaman substantif
                        setiap modul pelatihan.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div
                    class="bg-slate-50/70 p-6 rounded-2xl border border-slate-200/80 hover:border-amber-400 hover:bg-white shadow-xs hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2 group-hover:text-amber-700 transition-colors">
                        E-Sertifikat Bukti Dukung SKP
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sertifikat digital yang sah untuk dilampirkan dalam penilaian angka kredit dan e-Kinerja
                        pegawai di BKPPD Bengkalis.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== ALUR PEMBELAJARAN ASN (4 TAHAP CORPORATE UNIVERSITY) ==================== -->
    <section
        class="bg-gradient-to-b from-slate-50 via-emerald-50/30 to-slate-50 border-t border-slate-200/80 py-16 sm:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span
                    class="inline-block text-xs font-bold text-[#4D52B4] bg-[#4E9CE8]/15 border border-[#4E9CE8]/30 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    Tahapan Pembelajaran
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Alur Belajar Mandiri ASN dalam 4 Langkah
                </h2>
                <p class="text-sm text-slate-600 mt-2">
                    Mekanisme pembelajaran asinkronus yang terintegrasi dengan profil kepegawaian Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                <!-- Step 1 -->
                <div
                    class="relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-800 text-white font-black text-lg flex items-center justify-center mb-4 shadow-md shadow-emerald-800/20">
                        01
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Otentikasi Akun ASN</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Masuk menggunakan NIP atau alamat email kedinasan aktif Anda yang terdaftar pada sistem
                        kepegawaian
                        daerah.
                    </p>
                </div>

                <!-- Step 2 -->
                <div
                    class="relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 rounded-xl bg-teal-700 text-white font-black text-lg flex items-center justify-center mb-4 shadow-md shadow-teal-700/20">
                        02
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Pilih Jalur Kompetensi</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pilih modul kursus yang relevan dengan Sasaran Kinerja Pegawai (SKP) dan kebutuhan peningkatan
                        kompetensi unit kerja.
                    </p>
                </div>

                <!-- Step 3 -->
                <div
                    class="relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-700 text-white font-black text-lg flex items-center justify-center mb-4 shadow-md shadow-blue-700/20">
                        03
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">Microlearning & Kuis</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pelajari modul video, baca regulasi penunjang, dan kerjakan kuis evaluasi hingga tuntas mencapai
                        passing grade.
                    </p>
                </div>

                <!-- Step 4 -->
                <div
                    class="relative bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-shadow">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-500 text-white font-black text-lg flex items-center justify-center mb-4 shadow-md shadow-amber-500/20">
                        04
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-2">E-Sertifikat & Validasi JP</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Unduh sertifikat resmi sebagai bukti pemenuhan Jam Pelajaran (JP) dan lampiran
                        pelaporan
                        kinerja ASN.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== VERIFIKASI SERTIFIKAT ASN (INAgov VALIDATION PORTAL) ==================== -->
    <section id="verifikasi-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div
            class="relative rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 p-8 sm:p-12 text-white overflow-hidden shadow-2xl border border-emerald-500/30">
            <!-- Background Cyber Orbs -->
            <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-emerald-500/20 blur-3xl pointer-events-none">
            </div>
            <div class="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-amber-500/15 blur-3xl pointer-events-none">
            </div>

            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 border border-amber-400/30 text-amber-300 text-xs font-bold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Portal Validasi Keabsahan Dokumen Pengembangan Kompetensi ASN</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white">
                        Verifikasi Sertifikat Digital E-Belajar Bengkalis
                    </h2>

                    <p class="text-sm sm:text-base text-slate-300 max-w-xl leading-relaxed">
                        Digunakan oleh Atasan Langsung, BKPPD / BKPSDM Kabupaten Bengkalis, dan Tim Penilai Angka Kredit
                        untuk memverifikasi keaslian dokumen kelulusan dan jam pembelajaran (JP) yang diperoleh pegawai.
                    </p>
                </div>

                <div class="lg:col-span-5">
                    <form id="homeVerifyForm" action="{{ route('certificates.verify') }}" method="GET"
                        class="bg-slate-900/90 backdrop-blur-md p-6 rounded-2xl border border-slate-700/90 shadow-xl space-y-4">
                        <label for="homeCertInput" class="block text-xs font-semibold text-slate-300">
                            Masukkan Nomor Registrasi / Kode Unik Sertifikat
                        </label>
                        <div class="relative">
                            <input type="text" id="homeCertInput" name="code" required
                                placeholder="Contoh: BKPP-PKA/2026/09/00001 atau BKS-XXXX"
                                class="w-full px-4 py-3.5 pl-11 rounded-xl bg-slate-950/90 border border-slate-600 focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20 text-white placeholder-slate-500 text-sm font-medium transition-all">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl font-bold text-slate-950 bg-gradient-to-r from-amber-400 to-amber-300 hover:from-amber-300 hover:to-amber-200 transition-all text-sm flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 hover:scale-[1.01] active:scale-[0.99]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <span>Cek Validitas Sertifikat ASN</span>
                        </button>

                        <div class="text-[11px] text-slate-400 text-center flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Database sertifikat terintegrasi dan dapat diakses publik</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== FAQ KHUSUS ASN KABUPATEN BENGKALIS ==================== -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
        <div class="text-center mb-12">
            <span
                class="inline-block text-xs font-bold text-[#4D52B4] bg-[#4E9CE8]/15 border border-[#4E9CE8]/30 px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                Pusat Informasi & Regulasi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Pertanyaan yang Sering Diajukan ASN (FAQ)
            </h2>
            <p class="text-sm text-slate-600 mt-2">
                Pedoman pelaksanaan pembelajaran mandiri, penghitungan JP, dan sertifikasi ASN di Kabupaten Bengkalis.
            </p>
        </div>

        <div class="space-y-4">
            @forelse($faqs as $faq)
                <details
                    class="group bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 [&_summary::-webkit-details-marker]:none">
                    <summary
                        class="flex items-center justify-between cursor-pointer font-bold text-slate-900 text-sm sm:text-base list-none">
                        <div class="flex flex-col items-start gap-1.5">
                            @if($faq->category_name)
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded bg-[#4E9CE8]/10 text-[#4D52B4] border border-[#4E9CE8]/20 flex-shrink-0 w-fit">
                                    {{ $faq->category_name }}
                                </span>
                            @endif
                            <span class="mt-0.5">{{ $faq->question }}</span>
                        </div>
                        <span
                            class="faq-chevron ml-4 flex-shrink-0 transition-transform duration-200 text-slate-400 group-hover:text-[#4E9CE8]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </summary>
                    <div class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </details>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-slate-500 border border-slate-200">
                    <p class="text-sm">Belum ada pertanyaan umum yang diterbitkan.</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('faqs.index') }}"
                class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#4D52B4] hover:text-[#4E9CE8] hover:underline">
                <span>Lihat Seluruh Pertanyaan & Jawaban di Pusat Bantuan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </section>

    <!-- ==================== CALL TO ACTION BANNER (SMART ASN BENGKALIS BERMASA) ==================== -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 sm:pb-24">
        <div
            class="relative rounded-3xl bg-gradient-to-r from-[#2F3375] via-[#4D52B4] to-[#4E9CE8] p-8 sm:p-14 text-white overflow-hidden shadow-2xl text-center border border-[#4E9CE8]/30">
            <!-- Decorative Cyber Ambient -->
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-white/10 blur-2xl pointer-events-none">
            </div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 rounded-full bg-[#CAE5BC]/20 blur-2xl pointer-events-none">
            </div>

            <div class="relative max-w-3xl mx-auto space-y-6">
                <span
                    class="inline-block px-3.5 py-1 rounded-full bg-white/15 border border-white/20 text-[#CAE5BC] text-xs font-bold tracking-wider uppercase backdrop-blur-xs">
                    Wujudkan Birokrasi Berkelas Dunia
                </span>

                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Tingkatkan Kapasitas Smart ASN Anda Hari Ini
                </h2>

                <p class="text-sm sm:text-base text-white/90 leading-relaxed max-w-xl mx-auto">
                    Bergabung bersama ribuan ASN Pemerintah Kabupaten Bengkalis yang aktif meningkatkan kompetensi
                    mandiri
                    demi pelayanan publik yang bermarwah, maju, dan sejahtera.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="px-8 py-3.5 rounded-xl font-bold text-[#2F3375] bg-[#70D6C5] hover:bg-white shadow-lg shadow-black/20 hover:scale-105 active:scale-95 transition-all text-sm sm:text-base flex items-center gap-2">
                            <span>Buka Dashboard ASN</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="px-8 py-3.5 rounded-xl font-bold text-[#2F3375] bg-[#70D6C5] hover:bg-white shadow-lg shadow-black/20 hover:scale-105 active:scale-95 transition-all text-sm sm:text-base flex items-center gap-2">
                            <span>Daftar / Aktivasi Akun ASN</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endauth

                    <a href="{{ route('login') }}"
                        class="px-6 py-3.5 rounded-xl font-semibold text-white bg-[#2F3375]/70 hover:bg-[#2F3375] border border-white/20 backdrop-blur-md transition-all text-sm sm:text-base">
                        Katalog Modul Kompetensi
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== MODAL PANDUAN PENGGUNAAN LMS ASN ==================== -->
    <div id="guideModal"
        class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div
            class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 overflow-hidden">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Petunjuk Pembelajaran ASN</h3>
                        <p class="text-xs text-slate-500">Corporate University Pemerintah Kabupaten Bengkalis</p>
                    </div>
                </div>
                <button type="button" onclick="closeGuideModal()"
                    class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="py-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div class="rounded-2xl bg-slate-900 text-white p-5 relative overflow-hidden">
                    <div class="relative z-10">
                        <span
                            class="text-[10px] font-bold tracking-wider uppercase text-amber-400 bg-amber-400/20 px-2 py-0.5 rounded">
                            Regulasi Perkembangan Kompetensi ASN
                        </span>
                        <h4 class="text-base font-bold text-white mt-1">Hak & Kewajiban Pengembangan Kompetensi (20 JP)
                        </h4>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Berdasarkan UU No. 20 Tahun 2023, setiap ASN berhak mendapatkan pengembangan kompetensi
                            sekurang-kurangnya 20 Jam Pelajaran (JP) dalam satu tahun anggaran.
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div
                            class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex-shrink-0 flex items-center justify-center font-bold text-xs mt-0.5">
                            1</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Login dengan Akun ASN</div>
                            <div class="text-xs text-slate-600 mt-0.5">Masuk dengan NIP/Email yang terdaftar. Data
                                instansi
                                asal akan tercatat otomatis pada sertifikat kelulusan.</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div
                            class="w-7 h-7 rounded-lg bg-teal-600 text-white flex-shrink-0 flex items-center justify-center font-bold text-xs mt-0.5">
                            2</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Pilih Modul Pembelajaran Mandiri</div>
                            <div class="text-xs text-slate-600 mt-0.5">Pilih materi yang sesuai dengan Rencana Sasaran
                                Kinerja Pegawai (SKP) dan klik "Mulai Belajar".</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div
                            class="w-7 h-7 rounded-lg bg-blue-600 text-white flex-shrink-0 flex items-center justify-center font-bold text-xs mt-0.5">
                            3</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Selesaikan Pembelajaran & Asesmen Kuis</div>
                            <div class="text-xs text-slate-600 mt-0.5">Tonton video materi sampai tuntas dan ikuti kuis
                                evaluasi hingga mencapai passing grade kelulusan.</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70">
                        <div
                            class="w-7 h-7 rounded-lg bg-amber-500 text-white flex-shrink-0 flex items-center justify-center font-bold text-xs mt-0.5">
                            4</div>
                        <div>
                            <div class="text-xs font-bold text-slate-800">Klaim E-Sertifikat</div>
                            <div class="text-xs text-slate-600 mt-0.5">Sertifikat kelulusan resmi dapat diunduh langsung
                                untuk dilampirkan ke sistem e-Kinerja BKN / BKPPD Bengkalis.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeGuideModal()"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                    Tutup
                </button>
                <a href="{{ route('courses.index') }}"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors shadow-xs">
                    Jelajahi Modul Sekarang
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openGuideModal() {
            const modal = document.getElementById('guideModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeGuideModal() {
            const modal = document.getElementById('guideModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal on Escape key or outside click
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeGuideModal();
        });

        const guideModalEl = document.getElementById('guideModal');
        if (guideModalEl) {
            guideModalEl.addEventListener('click', function (e) {
                if (e.target === guideModalEl) closeGuideModal();
            });
        }
    </script>
@endpush