<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - E-Belajar Kabupaten Bengkalis</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bengkalis: {
                            green: '#047857',
                            dark: '#064E3B',
                            gold: '#F59E0B',
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

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col md:flex-row">
    <!-- Sidebar -->
    <aside
        class="w-full md:w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col justify-between border-r border-slate-800">
        <div>
            <!-- Brand -->
            <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                    class="w-10 h-10 object-contain">
                <div>
                    <div class="font-extrabold text-white text-base leading-tight">E-Belajar Panel</div>
                    <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">
                        {{ Auth::user()->isSuperAdmin() ? 'Super Admin' : 'Admin Kursus' }}
                    </div>
                </div>
            </div>

            <!-- Nav Items -->
            <div class="p-4 space-y-1.5 text-sm font-medium">
                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold px-3 py-2">Navigasi Utama
                </div>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold px-3 pt-4 pb-2">Manajemen
                    Konten</div>

                <a href="{{ route('admin.courses.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.courses.*') ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Kelola Pembelajaran & Builder</span>
                </a>

                <a href="{{ route('admin.faqs.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ (request()->routeIs('admin.faqs.*') || request()->routeIs('admin.faq-categories.*')) ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pertanyaan Umum (FAQ)</span>
                </a>

                @if(Auth::user()->isSuperAdmin())
                    <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold px-3 pt-4 pb-2">Sistem & Audit
                    </div>

                    <a href="{{ route('superadmin.users.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('superadmin.users.*') ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </a>

                    <a href="{{ route('superadmin.activity-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('superadmin.activity-logs.*') ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Audit Log Aktivitas</span>
                    </a>
                @endif

                <div class="text-[11px] uppercase tracking-wider text-slate-500 font-bold px-3 pt-4 pb-2">Akun Saya</div>

                <a href="{{ route('profile.edit', Auth::id()) }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('profile.*') ? 'bg-emerald-700 text-white font-semibold shadow-xs' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Edit Profil & Password</span>
                </a>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 space-y-2">
            <a href="{{ route('home') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Lihat Portal Publik</span>
            </a>

            <div class="p-2.5 bg-slate-800/80 rounded-xl flex items-center justify-between gap-2.5 border border-slate-700/50">
                <a href="{{ route('profile.edit', Auth::id()) }}" class="flex items-center gap-2.5 min-w-0 flex-1 hover:opacity-90 group transition-all" title="Edit Profil & Password">
                    <div class="relative shrink-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                                class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500/50 shadow-xs">
                        @else
                            <div class="w-8 h-8 rounded-full gradient-bengkalis text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500/50 shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-slate-900" title="Online"></span>
                    </div>
                    <div class="truncate">
                        <div class="text-xs font-bold text-white truncate group-hover:text-emerald-300 transition-colors">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar dari akun" class="text-slate-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-slate-700/50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header
            class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Panel</a>
                <span>/</span>
                <span class="font-semibold text-slate-800">@yield('page_title', 'Dashboard')</span>
            </div>

            <div class="flex items-center gap-3">
                <span
                    class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ Auth::user()->isSuperAdmin() ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300' }}">
                    Role: {{ strtoupper(Auth::user()->role) }}
                </span>

                <!-- User Profile & Avatar -->
                <a href="{{ route('profile.edit', Auth::id()) }}"
                    title="Edit Profil & Password"
                    class="flex items-center gap-2.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all shadow-xs group">
                    <div class="relative shrink-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}"
                                class="w-8 h-8 rounded-full object-cover ring-2 ring-emerald-500/50 group-hover:ring-emerald-400 transition-all shadow-xs">
                        @else
                            <div class="w-8 h-8 rounded-full gradient-bengkalis text-white font-bold text-xs flex items-center justify-center ring-2 ring-emerald-500/50 group-hover:ring-emerald-400 transition-all shadow-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-slate-900" title="Online"></span>
                    </div>
                    <div class="text-left hidden md:block max-w-[140px] truncate">
                        <div class="text-xs font-bold text-white truncate group-hover:text-emerald-300 transition-colors">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-emerald-400 truncate">Edit Profil &rarr;</div>
                    </div>
                </a>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Keluar dari akun" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold text-slate-500 transition-all duration-200 hover:bg-red-50 hover:text-red-600 border border-slate-200">
                        <svg class="h-4 w-4 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Flash messages -->
        <div class="p-6 pb-0">
            @if($errors->any())
                <div class="p-4 mb-4 text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-sm mb-1 text-red-900">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>Terjadi kesalahan validasi:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5 ml-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div
                    class="flex items-center p-4 mb-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-xs">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center p-4 mb-4 text-red-800 rounded-xl bg-red-50 border border-red-200 shadow-xs">
                    <svg class="flex-shrink-0 w-5 h-5 mr-3 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif
        </div>

        <main class="p-6 flex-1">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>