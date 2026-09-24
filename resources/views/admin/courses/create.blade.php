@extends('layouts.admin')

@section('title', 'Buat Pembelajaran Baru')
@section('page_title', 'Tambah Pembelajaran')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-xl font-black text-slate-900">Form Pembuatan Pembelajaran Baru</h2>
            <!-- <p class="text-xs text-slate-500 mt-0.5">Setelah membuat informasi dasar kursus, Anda akan diarahkan ke Course
                                    Builder untuk menyusun modul & materi.</p> -->
        </div>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul
                    Pembelajaran
                    *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    placeholder="Contoh: Pengelolaan Administrasi Desa Digital"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('title')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="category_id"
                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Pembelajaran
                    *</label>
                <select name="category_id" id="category_id" required
                    class="w-full px-3 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="duration"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Estimasi Durasi
                        (Menit)</label>
                    <input type="number" name="duration" id="duration" value="{{ old('duration', 60) }}" min="0"
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status
                        Awal *</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden bg-white">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep Tertutup)
                        </option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Langsung
                            Tayang)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description"
                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Lengkap
                    Pembelajaran</label>
                <textarea name="description" id="description" rows="4"
                    placeholder="Tuliskan tujuan dan ringkasan pembelajaran..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">{{ old('description') }}</textarea>
            </div>

            <div>
                <label for="thumbnail" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Gambar
                    Thumbnail (Opsional)</label>
                <input type="file" name="thumbnail" id="thumbnail" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="certificate_enabled" value="1" checked
                        class="w-4 h-4 text-emerald-600 rounded-sm focus:ring-emerald-500">
                    <span class="text-xs font-bold text-slate-800">Terbitkan Sertifikat Resmi Otomatis Setelah Kursus 100%
                        Selesai</span>
                </label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.courses.index') }}"
                    class="text-xs font-bold text-slate-500 hover:text-slate-800">&larr; Batal</a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan & Lanjutkan ke Kelola Pembelajaran &rarr;
                </button>
            </div>
        </form>
    </div>
@endsection