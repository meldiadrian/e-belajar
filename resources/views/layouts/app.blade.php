<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Belajar Kabupaten Bengkalis') - Portal Pembelajaran Digital</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS (via CDN fallback / Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            850: '#064E3B',
                            950: '#022C22',
                        },
                        bengkalis: {
                            green: '#047857',
                            dark: '#064E3B',
                            gold: '#F59E0B',
                            golddark: '#D97706',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .gradient-bengkalis {
            background: linear-gradient(135deg, #064E3B 0%, #047857 50%, #0D9488 100%);
        }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased">
    <!-- Top Announcement Bar -->
    <!-- <div class="bg-amber-500 text-amber-950 px-4 py-1.5 text-xs font-semibold text-center flex items-center justify-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-amber-900 animate-pulse"></span>
        <span>Portal Resmi Pembelajaran Mandiri Pemerintah Kabupaten Bengkalis, Provinsi Riau</span>
    </div> -->

    <!-- Main Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand & Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                            class="w-10 h-10 object-contain group-hover:scale-105 transition-transform">
                        <div>
                            <div class="font-extrabold text-lg text-emerald-900 leading-tight tracking-tight">E-Belajar
                            </div>
                            <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-widest">Kab.
                                Bengkalis</div>
                        </div>
                    </a>

                    <!-- Nav Links Desktop -->
                    <div class="hidden md:flex items-center space-x-1 ml-8">
                        <a href="{{ route('home') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-100' }}">Beranda</a>
                        <!-- <a href="{{ route('courses.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('courses.*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-100' }}">Lihat
                            Katalog</a> -->
                        <!-- <a href="{{ route('certificates.verify', 'SAMPLE') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-emerald-700 hover:bg-slate-100">Verifikasi
                            Sertifikat</a> -->
                        <a href="{{ route('faqs.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('faqs.*') ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-100' }}">Pertanyaan
                            Umum</a>
                    </div>
                </div>

                <!-- Right Actions / Profile -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Dashboard Button -->
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard ({{ ucfirst(Auth::user()->role) }})</span>
                        </a>

                        @if(Auth::user()->isUser())
                            <a href="{{ route('my.courses') }}"
                                class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                                <span>Pembelajaran Saya</span>
                            </a>
                        @endif

                        <!-- User Profile Menu -->
                        <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                            <a href="{{ route('profile.edit', Auth::id()) }}" title="Edit Profil Saya"
                                class="text-right hidden sm:block hover:opacity-80 transition-opacity">
                                <div class="text-xs font-bold text-slate-800 flex items-center gap-1">
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-medium text-emerald-700 uppercase">
                                    {{ Auth::user()->institution ?? Auth::user()->role }}
                                </div>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar"
                                    class="p-2 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-4 py-2 text-sm font-semibold text-emerald-800 hover:text-emerald-950 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="px-4 py-2 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-xs transition-all hover:shadow-md">Daftar
                            Sekarang</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Notifications / Toasts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-xs"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-center p-4 mb-4 text-blue-800 rounded-xl bg-blue-50 border border-blue-200 shadow-xs"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm font-medium">{{ session('info') }}</div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-16 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                            class="w-9 h-9 object-contain">
                        <span class="text-white font-bold text-lg">E-Belajar Kabupaten Bengkalis</span>
                    </div>
                    <p class="text-sm text-slate-400 max-w-md leading-relaxed">
                        Platform peningkatan kompetensi aparatur dan masyarakat berbasis digital yang transparan,
                        terukur, dan bersertifikasi resmi Pemerintah Kabupaten Bengkalis.
                    </p>
                    <div class="mt-4 text-xs text-emerald-400 font-medium">
                        Dikelola oleh Dinas Komunikasi, Informatika dan Statistik Kabupaten Bengkalis.
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('courses.index') }}"
                                class="hover:text-emerald-400 transition-colors">Lihat Katalog</a></li>

                        <li><a href="{{ route('faqs.index') }}"
                                class="hover:text-emerald-400 transition-colors">Pertanyaan Umum</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Portal
                                Masuk</a></li>
                        <li><a href="{{ route('register') }}"
                                class="hover:text-emerald-400 transition-colors">Pendaftaran Akun Baru</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Pusat Bantuan</h4>
                    <p class="text-sm text-slate-400 leading-relaxed mb-2">
                        Jl. Kartini No. 012 Bengkalis Riau</p>
                    <p class="text-sm text-slate-400">Email: diskominfotik@bengkaliskab.go.id</p>
                    <p class="text-sm text-slate-400">Portal: diskominfotik.bengkaliskab.go.id</p>
                </div>
            </div>

            <div
                class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Pemerintah Kabupaten Bengkalis. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex space-x-4 mt-2 sm:mt-0">
                    <span>Versi 1.0.0 (LMS Bengkalis)</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>