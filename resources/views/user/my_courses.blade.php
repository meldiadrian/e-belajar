@extends('layouts.admin')

@section('title', 'Pembelajaran Saya - E-Belajar Kabupaten Bengkalis')
@section('page_title', 'Pembelajaran Saya')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900">Pembelajaran Yang Saya Ikuti</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola dan lanjutkan seluruh proses pembelajaran mandiri
                    Anda</p>
            </div>
            <a href="{{ route('courses.index') }}"
                class="px-4 py-2 bg-[#4D52B4] text-white rounded-xl text-xs font-bold hover:bg-[#4E9CE8] shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                + Tambah Pembelajaran Lainnya
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($enrollments as $enrollment)
                <div
                    class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <span
                                class="text-[10px] font-bold uppercase tracking-wider text-[#4D52B4] bg-[#4E9CE8]/20 px-2 py-0.5 rounded-full">
                                {{ $enrollment->course->category->name ?? 'Program' }}
                            </span>
                            <span
                                class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $enrollment->status === 'completed' ? 'bg-[#4E9CE8]/20 text-[#4D52B4]' : 'bg-amber-100 text-amber-800' }}">
                                {{ $enrollment->status === 'completed' ? 'Selesai' : 'Aktif' }}
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 mt-2 line-clamp-2">
                            <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                class="hover:text-[#4E9CE8]">
                                {{ $enrollment->course->title }}
                            </a>
                        </h3>

                        <div class="mt-6 space-y-1.5">
                            <div class="flex justify-between text-xs text-slate-600 font-semibold">
                                <span>Kemajuan Belajar</span>
                                <span>{{ $enrollment->progress->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-[#70D6C5] h-2.5 rounded-full"
                                    style="width: {{ $enrollment->progress->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-0 border-t border-slate-100 mt-4">
                        <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}"
                            class="block w-full py-2.5 text-center text-xs font-bold text-white bg-[#4D52B4] hover:bg-[#4E9CE8] rounded-xl shadow-xs hover:shadow-md hover:-translate-y-0.5 transition-all duration-300">
                            {{ ($enrollment->progress->progress_percentage ?? 0) >= 100 ? 'Buka Kembali Materi' : 'Lanjutkan Belajar' }}
                            &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-3 bg-white p-12 sm:p-20 rounded-3xl border border-slate-200 text-center flex flex-col items-center justify-center">
                    <div class="w-32 h-32 mb-6 bg-[#4E9CE8]/10 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-[#4D52B4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Belum Ada Pembelajaran</h3>
                    <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto">Anda belum mendaftar pada pembelajaran apapun. Yuk, jelajahi katalog pelatihan dan mulai tingkatkan kompetensi Anda sekarang!</p>
                    <a href="{{ route('courses.index') }}" class="inline-block px-8 py-3.5 bg-[#4D52B4] text-white rounded-xl text-sm font-bold hover:bg-[#4E9CE8] shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        Lihat Katalog Pembelajaran
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $enrollments->links() }}
        </div>
    </div>
@endsection