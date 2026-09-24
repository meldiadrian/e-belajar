@extends('layouts.admin')

@section('title', 'Edit FAQ')
@section('page_title', 'Perbarui Pertanyaan Umum')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div>
            <h2 class="text-lg font-black text-slate-900">Perbarui Data FAQ</h2>
            <p class="text-xs text-slate-500 mt-0.5">Edit teks pertanyaan, penjelasan jawaban, kategori, atau status publikasi.</p>
        </div>

        <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Question -->
            <div>
                <label for="question" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Pertanyaan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="question" id="question" value="{{ old('question', $faq->question) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('question')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Answer -->
            <div>
                <label for="answer" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Jawaban <span class="text-red-500">*</span>
                </label>
                <textarea name="answer" id="answer" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden leading-relaxed">{{ old('answer', $faq->answer) }}</textarea>
                @error('answer')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="faq_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <a href="{{ route('admin.faq-categories.create') }}" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:underline">
                            + Tambah Kategori
                        </a>
                    </div>
                    <select name="faq_category_id" id="faq_category_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('faq_category_id', $faq->faq_category_id) == $cat->id || $faq->category === $cat->name) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('faq_category_id')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Urutan Tampil <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $faq->order) }}" min="0" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    <span class="text-[11px] text-slate-400 mt-1 block">Angka lebih kecil tampil lebih awal.</span>
                    @error('order')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Published Checkbox -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $faq->is_published) ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-slate-300">
                    <div>
                        <span class="text-sm font-bold text-slate-800">Publikasikan</span>
                        <p class="text-xs text-slate-500">Jika dicentang, pertanyaan ini aktif dan tampil di halaman publik FAQ.</p>
                    </div>
                </label>
            </div>

            <!-- Submit & Cancel -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.faqs.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
