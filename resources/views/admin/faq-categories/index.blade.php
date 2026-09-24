@extends('layouts.admin')

@section('title', 'Manajemen Kategori FAQ')
@section('page_title', 'Kelola Kategori Pertanyaan Umum')

@section('content')
<div class="space-y-6">

    <!-- Header Action Bar & Sub Navigation -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-800">Kategori Pertanyaan Umum (FAQ)</h2>
                <p class="text-xs text-slate-500 mt-1">Kelola kategori tanya jawab agar pertanyaan tersusun rapi secara tematik.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Daftar Pertanyaan</span>
                </a>
                <a href="{{ route('admin.faq-categories.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Kategori Baru</span>
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 text-xs font-bold pt-2 gap-4">
            <a href="{{ route('admin.faqs.index') }}" class="pb-3 text-slate-500 hover:text-emerald-700 border-b-2 border-transparent transition-colors">
                Daftar Pertanyaan
            </a>
            <a href="{{ route('admin.faq-categories.index') }}" class="pb-3 text-emerald-700 border-b-2 border-emerald-600">
                Kategori Pertanyaan
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.faq-categories.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau deskripsi kategori..." class="w-full px-4 py-2 rounded-xl border border-slate-300 text-xs focus:border-emerald-600 focus:outline-hidden">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold text-xs transition-colors shrink-0">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.faq-categories.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition-colors flex items-center shrink-0">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5 w-16 text-center">Urutan</th>
                        <th class="px-5 py-3.5">Nama & Deskripsi Kategori</th>
                        <th class="px-5 py-3.5 w-36 text-center">Jumlah FAQ</th>
                        <th class="px-5 py-3.5 w-28 text-center">Status</th>
                        <th class="px-5 py-3.5 w-36 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 text-center font-bold text-slate-700">
                                <span class="w-7 h-7 rounded-lg bg-slate-100 inline-flex items-center justify-center font-mono">
                                    {{ $category->order }}
                                </span>
                            </td>
                            <td class="px-5 py-4 space-y-1">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ $category->name }}
                                </div>
                                @if($category->description)
                                    <div class="text-slate-500 leading-relaxed">
                                        {{ $category->description }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Tidak ada deskripsi</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('admin.faqs.index', ['category' => $category->name]) }}" class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 font-bold hover:bg-emerald-100 transition-colors inline-block" title="Lihat daftar pertanyaan di kategori ini">
                                    {{ $category->faqs_count }} Pertanyaan
                                </a>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($category->is_active)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-200 text-slate-600 font-bold text-[10px]">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.faq-categories.edit', $category) }}" class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold transition-colors" title="Edit Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.faq-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Pertanyaan yang terkait tidak akan terhapus.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 font-bold transition-colors" title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                Belum ada kategori FAQ. Silakan klik tombol "Tambah Kategori Baru".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
