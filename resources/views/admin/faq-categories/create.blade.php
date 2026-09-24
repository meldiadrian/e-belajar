@extends('layouts.admin')

@section('title', 'Tambah Kategori FAQ')
@section('page_title', 'Tambah Kategori FAQ')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div>
            <h2 class="text-lg font-black text-slate-900">Formulir Tambah Kategori</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kategori ini akan digunakan untuk mengelompokkan pertanyaan di halaman FAQ.</p>
        </div>

        <form action="{{ route('admin.faq-categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Kuis & Ujian Mandiri" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('name')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi Kategori (Opsional)
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Uraian singkat seputar topik kategori ini..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Order -->
            <div>
                <label for="order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Urutan Tampil <span class="text-red-500">*</span>
                </label>
                <input type="number" name="order" id="order" value="{{ old('order', $nextOrder) }}" min="0" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                <span class="text-[11px] text-slate-400 mt-1 block">Angka lebih kecil akan tampil lebih awal pada filter kategori.</span>
                @error('order')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Active Checkbox -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-slate-300">
                    <div>
                        <span class="text-sm font-bold text-slate-800">Status Aktif</span>
                        <p class="text-xs text-slate-500">Jika aktif, kategori ini akan muncul pada pilihan kategori saat membuat FAQ dan di filter publik.</p>
                    </div>
                </label>
            </div>

            <!-- Submit & Cancel -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.faq-categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
