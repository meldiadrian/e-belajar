@extends('layouts.admin')

@section('title', 'Tambah Penandatangan Sertifikat')
@section('page_title', 'Tambah Penandatangan Sertifikat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div>
            <h2 class="text-lg font-black text-slate-900">Formulir Penandatangan Sertifikat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Isi data pejabat dan unggah gambar tanda tangan berserta stempel resmi untuk dicantumkan pada sertifikat.</p>
        </div>

        <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: AGUS SOFYAN, S.STP.,MPA" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('name')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- NIP -->
            <div>
                <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    NIP <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nip" id="nip" value="{{ old('nip') }}" required placeholder="Contoh: 197908161998021001" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden font-mono">
                @error('nip')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jabatan -->
                <div>
                    <label for="jabatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Jabatan
                    </label>
                    <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', 'Kepala Dinas Komunikasi, Informatika dan Statistik') }}" placeholder="Contoh: Kepala Dinas Komunikasi, Informatika dan Statistik" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    @error('jabatan')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Instansi -->
                <div>
                    <label for="instansi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Instansi / Daerah
                    </label>
                    <input type="text" name="instansi" id="instansi" value="{{ old('instansi', 'Kabupaten Bengkalis') }}" placeholder="Contoh: Kabupaten Bengkalis" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    @error('instansi')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Upload Signature Image -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Upload Gambar TTD & Stempel
                </label>
                <p class="text-xs text-slate-500 mb-2">Unggah file gambar transparan (PNG direkomendasikan) berisi stempel dan tanda tangan.</p>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-emerald-500 transition-colors bg-slate-50/50">
                    <div class="space-y-2 text-center">
                        <div id="preview-container" class="hidden mb-3">
                            <img id="image-preview" src="#" alt="Pratinjau TTD" class="mx-auto h-24 w-auto object-contain border border-slate-200 rounded-lg p-1 bg-white shadow-xs">
                        </div>
                        <svg id="upload-icon" class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-xs text-slate-600 justify-center">
                            <label for="signature_image" class="relative cursor-pointer bg-white rounded-md font-bold text-emerald-700 hover:text-emerald-800 focus-within:outline-hidden px-2 py-1 border border-slate-200 shadow-2xs">
                                <span>Pilih Berkas Gambar</span>
                                <input id="signature_image" name="signature_image" type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400">PNG, JPG, WEBP hingga 2MB (Background transparan direkomendasikan)</p>
                    </div>
                </div>
                @error('signature_image')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Active Checkbox -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-slate-300">
                    <div>
                        <span class="text-sm font-bold text-slate-800">Jadikan Penandatangan Aktif</span>
                        <p class="text-xs text-slate-500">Jika dicentang, penandatangan ini otomatis digunakan pada semua sertifikat kelulusan yang dicetak/dilihat.</p>
                    </div>
                </label>
            </div>

            <!-- Submit & Cancel -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.certificates.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors">
                    Simpan Penandatangan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function previewImage(input) {
    const previewContainer = document.getElementById('preview-container');
    const preview = document.getElementById('image-preview');
    const uploadIcon = document.getElementById('upload-icon');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
            uploadIcon.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
