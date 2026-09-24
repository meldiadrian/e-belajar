@extends('layouts.app')

@section('title', 'Pertanyaan Umum (FAQ) - E-Belajar Kabupaten Bengkalis')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Hero Header -->
            <div class="text-center space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pusat Bantuan & Informasi</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h1>
                <p class="text-slate-600 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed">
                    Temukan panduan dan jawaban cepat seputar pendaftaran akun, kursus pembelajaran mandiri, pelaksanaan
                    kuis, serta verifikasi sertifikat di E-Belajar Pemerintah Kabupaten Bengkalis.
                </p>

                @auth
                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                        <div class="pt-2">
                            <a href="{{ route('admin.faqs.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition-all hover:scale-105">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Kelola Data FAQ (Admin Panel)</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            <!-- Search & Filter Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <form method="GET" action="{{ route('faqs.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="q" value="{{ $search }}"
                            placeholder="Ketik kata kunci pertanyaan (misal: kuis, daftar)..."
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    </div>
                    @if($selectedCategory)
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm shadow-xs transition-colors shrink-0">
                            Cari
                        </button>
                        @if($search || $selectedCategory)
                            <a href="{{ route('faqs.index') }}"
                                class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-colors shrink-0 flex items-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Category Pills -->
                @if($categories->count() > 0)
                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-100 text-xs">
                        <span class="font-bold text-slate-500 mr-1">Kategori:</span>
                        <a href="{{ route('faqs.index', array_filter(['q' => $search])) }}"
                            class="px-3 py-1.5 rounded-full font-semibold transition-all {{ empty($selectedCategory) ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Kategori
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('faqs.index', array_filter(['category' => $cat, 'q' => $search])) }}"
                                class="px-3 py-1.5 rounded-full font-semibold transition-all {{ $selectedCategory === $cat ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- FAQs Accordion List -->
            <div class="space-y-4">
                @forelse($faqs as $index => $faq)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden transition-all duration-200 hover:border-emerald-300 faq-item"
                        id="faq-{{ $faq->id }}">
                        <button type="button" onclick="toggleFaq({{ $faq->id }})"
                            class="w-full px-6 py-5 text-left flex items-start justify-between gap-4 focus:outline-hidden group">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 text-[11px] font-bold">
                                        {{ $faq->category_name }}
                                    </span>
                                </div>
                                <h3
                                    class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">
                                    {{ $faq->question }}
                                </h3>
                            </div>
                            <div class="shrink-0 p-1.5 rounded-lg bg-slate-100 group-hover:bg-emerald-100 transition-colors text-slate-500 group-hover:text-emerald-800"
                                id="icon-{{ $faq->id }}">
                                <svg class="w-5 h-5 transform transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <div id="answer-{{ $faq->id }}"
                            class="hidden px-6 pb-6 pt-1 text-sm text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-3">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Tidak ada pertanyaan yang cocok</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Coba gunakan kata kunci pencarian yang berbeda atau pilih kategori lain.
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('faqs.index') }}"
                                class="px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors inline-block">
                                Lihat Semua Pertanyaan
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Help Desk Footer Callout -->
            <div
                class="p-8 rounded-3xl bg-gradient-to-r from-emerald-800 to-teal-900 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-md">
                <div class="space-y-2 text-center md:text-left">
                    <h3 class="text-xl font-bold">Masih Membutuhkan Informasi Tambahan?</h3>
                    <p class="text-xs sm:text-sm text-emerald-100 max-w-xl">
                        Jika pertanyaan Anda belum terjawab di sini, silakan hubungi tim pengelola melalui kontak resmi
                        Diskominfotik Kabupaten Bengkalis.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 shrink-0">
                    <a href="mailto:diskominfo@bengkalis.go.id"
                        class="px-5 py-3 rounded-xl bg-white text-emerald-950 font-bold text-xs hover:bg-emerald-50 transition-colors shadow-sm">
                        Kirim Email Bantuan
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleFaq(id) {
            const answer = document.getElementById('answer-' + id);
            const icon = document.getElementById('icon-' + id)?.querySelector('svg');

            if (answer) {
                const isHidden = answer.classList.contains('hidden');
                if (isHidden) {
                    answer.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                } else {
                    answer.classList.add('hidden');
                    if (icon) icon.classList.remove('rotate-180');
                }
            }
        }
    </script>
@endsection