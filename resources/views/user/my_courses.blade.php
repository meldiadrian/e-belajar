@extends('layouts.app')

@section('title', 'Kursus Saya - E-Belajar Kabupaten Bengkalis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Kursus Yang Saya Ikuti</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola dan lanjutkan seluruh proses pembelajaran mandiri Anda</p>
        </div>
        <a href="{{ route('courses.index') }}" class="px-4 py-2 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800 shadow-xs">
            + Tambah Kursus Lainnya
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($enrollments as $enrollment)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-lg transition-all overflow-hidden flex flex-col justify-between">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
                            {{ $enrollment->course->category->name ?? 'Program' }}
                        </span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $enrollment->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $enrollment->status === 'completed' ? 'Selesai' : 'Aktif' }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mt-2 line-clamp-2">
                        <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}" class="hover:text-emerald-700">
                            {{ $enrollment->course->title }}
                        </a>
                    </h3>

                    <div class="mt-6 space-y-1.5">
                        <div class="flex justify-between text-xs text-slate-600 font-semibold">
                            <span>Kemajuan Belajar</span>
                            <span>{{ $enrollment->progress->progress_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-emerald-600 h-2.5 rounded-full" style="width: {{ $enrollment->progress->progress_percentage ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="p-6 pt-0 border-t border-slate-100 mt-4">
                    <a href="{{ route('learning.course', $enrollment->course->slug ?? $enrollment->course->id) }}" class="block w-full py-2.5 text-center text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl transition-colors shadow-xs">
                        {{ ($enrollment->progress->progress_percentage ?? 0) >= 100 ? 'Buka Kembali Materi' : 'Lanjutkan Belajar' }} &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white p-12 rounded-2xl border border-slate-200 text-center">
                <p class="text-slate-400 text-sm mb-4">Anda belum mendaftar pada kursus apapun.</p>
                <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800 shadow-md">
                    Lihat Semua Kursus
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $enrollments->links() }}
    </div>
</div>
@endsection
