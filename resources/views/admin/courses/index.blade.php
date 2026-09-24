@extends('layouts.admin')

@section('title', 'Manajemen Kursus')
@section('page_title', 'Kelola Pembelajaran')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900">Daftar Pembelajaran</h2>
                <p class="text-xs text-slate-500">Kelola kurikulum, modul pembelajaran, dan asesmen kuis</p>
            </div>
            <a href="{{ route('admin.courses.create') }}"
                class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                + Buat Pembelajaran Baru
            </a>
        </div>

        <!-- Courses Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-100 font-bold">
                        <tr>
                            <th class="py-3.5 px-4">Kursus</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Struktur</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4">Peserta</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($courses as $course)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $course->title }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Pembuat:
                                        {{ $course->creator->name ?? 'Admin' }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="font-semibold text-slate-800 block">{{ $course->category->name ?? '-' }}</span>
                                </td>
                                <td class="py-4 px-4 font-semibold text-slate-700">
                                    <div>{{ $course->modules_count }} Modul</div>
                                    <div class="text-[11px] text-slate-400 font-normal">{{ $course->lessons_count }} Pelajaran
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($course->status === 'draft' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $course->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 font-bold text-slate-800">
                                    {{ $course->enrollments_count }}
                                </td>
                                <td class="py-4 px-4 text-right space-x-2 whitespace-nowrap">
                                    <form action="{{ route('admin.courses.toggle-publish', $course->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold {{ $course->status === 'published' ? 'bg-amber-50 text-amber-800 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                                            {{ $course->status === 'published' ? 'Jadikan Draft' : 'Publikasikan' }}
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.courses.builder', $course->id) }}"
                                        class="px-3 py-1.5 rounded-lg text-[11px] font-bold bg-slate-900 text-white hover:bg-slate-800">
                                        Buka Builder &rarr;
                                    </a>

                                    <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kursus ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 p-1">
                                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum ada kursus yang dibuat. Silakan
                                    tambahkan Pembelajaran pertama Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $courses->links() }}
        </div>
    </div>
@endsection