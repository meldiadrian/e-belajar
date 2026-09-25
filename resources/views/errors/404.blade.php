@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan | E-Belajar Kabupaten Bengkalis')

@section('content')
    <div class="relative min-h-[75vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-16 overflow-hidden">
        <!-- Ambient Glowing Background Accents -->
        <div
            class="absolute -top-20 -left-20 w-80 sm:w-96 h-80 sm:h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none -z-10">
        </div>
        <div
            class="absolute -bottom-20 -right-20 w-80 sm:w-96 h-80 sm:h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none -z-10">
        </div>

        <div class="w-full max-w-2xl mx-auto text-center">
            <!-- Status Badge with Pulsing Security Dot -->
            <div
                class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-slate-900/5 border border-slate-200/80 shadow-xs mb-6">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-600"></span>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Kode Status: HTTP 404</span>
                <span class="text-slate-300">|</span>
                <span class="text-xs font-medium text-slate-500">Resource Not Found</span>
            </div>

            <!-- Custom 404 Illustration / Graphic -->
            <div class="relative flex items-center justify-center my-4">
                <!-- Large Backdrop Typography -->
                <div
                    class="text-8xl sm:text-9xl lg:text-[11rem] font-black tracking-tighter bg-gradient-to-br from-emerald-800 via-emerald-600 to-amber-500 bg-clip-text text-transparent select-none drop-shadow-sm opacity-90 leading-none">
                    404
                </div>

                <!-- Floating Central Icon Over Badge -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/90 backdrop-blur-md border border-slate-200 shadow-xl flex items-center justify-center transform -rotate-3 hover:rotate-0 transition-transform">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-emerald-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Headings -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight mt-6">
                Halaman Tidak Ditemukan
            </h1>
            <p class="text-sm sm:text-base text-slate-600 max-w-lg mx-auto mt-3 leading-relaxed">
                Tautan yang Anda tuju tidak tersedia, telah dipindahkan, atau akses sementara dibatasi oleh sistem proteksi
                keamanan.
            </p>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>

                <button type="button"
                    onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('home') }}'"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-sm shadow-xs transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Halaman Sebelumnya</span>
                </button>

                <a href="{{ route('faqs.index') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 font-semibold text-sm transition-all duration-200">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pusat Bantuan (FAQ)</span>
                </a>
            </div>


            <!-- Security Notice Banner -->
            <div class="mt-8 p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-left flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div class="text-xs text-emerald-950 leading-relaxed">
                    <span class="font-bold">Keamanan Akun Terproteksi:</span> Jika pengalihan ini terjadi setelah beberapa
                    percobaan masuk yang tidak berhasil, sistem telah membatasi akses sementara demi menjaga keamanan akun
                    Anda. Silakan coba kembali beberapa saat lagi.
                </div>
            </div>
        </div>
    </div>
@endsection