@extends('layouts.admin')

@section('title', 'Ubah Penandatangan Sertifikat')
@section('page_title', 'Ubah Penandatangan Sertifikat')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Card -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-slate-900">Ubah Data Penandatangan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, NIP, atau gambar tanda tangan pejabat bersangkutan.</p>
            </div>
            @if($signer->is_active)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Aktif
                </span>
            @endif
        </div>

        <form action="{{ route('admin.certificates.update', $signer->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Nama Lengkap beserta Gelar <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $signer->name) }}" required placeholder="Contoh: AGUS SOFYAN, S.STP.,MPA" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                @error('name')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- NIP -->
            <div>
                <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    NIP <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nip" id="nip" value="{{ old('nip', $signer->nip) }}" required placeholder="Contoh: 197908161998021001" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden font-mono">
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
                    <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $signer->jabatan) }}" placeholder="Contoh: Kepala Dinas Komunikasi, Informatika dan Statistik" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    @error('jabatan')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Instansi -->
                <div>
                    <label for="instansi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Instansi / Daerah
                    </label>
                    <input type="text" name="instansi" id="instansi" value="{{ old('instansi', $signer->instansi) }}" placeholder="Contoh: Kabupaten Bengkalis" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    @error('instansi')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Upload Signature Image -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Gambar TTD & Stempel
                </label>
                <p class="text-xs text-slate-500 mb-2">Unggah file baru jika ingin mengganti tanda tangan & stempel saat ini.</p>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-emerald-500 transition-colors bg-slate-50/50">
                    <div class="space-y-3 text-center">
                        <div id="preview-container" class="{{ $signer->signature_image ? '' : 'hidden' }} mb-2">
                            <img id="image-preview" 
                                 src="{{ $signer->signature_image ? asset('storage/' . $signer->signature_image) : '#' }}" 
                                 alt="Pratinjau TTD" 
                                 class="mx-auto h-28 w-auto object-contain border border-slate-200 rounded-lg p-2 bg-white shadow-xs">
                            <p id="current-label" class="text-[11px] text-slate-400 mt-1 font-medium">Gambar saat ini</p>
                        </div>
                        
                        <div class="flex text-xs text-slate-600 justify-center">
                            <label for="signature_image" class="relative cursor-pointer bg-white rounded-md font-bold text-emerald-700 hover:text-emerald-800 focus-within:outline-hidden px-3 py-1.5 border border-slate-200 shadow-2xs">
                                <span>{{ $signer->signature_image ? 'Ganti File Gambar' : 'Pilih Berkas Gambar' }}</span>
                                <input id="signature_image" name="signature_image" type="file" accept="image/png,image/jpeg,image/webp" class="sr-only" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400">PNG, JPG, WEBP hingga 2MB (Kosongkan jika tidak ingin mengubah gambar)</p>
                    </div>
                </div>
                @error('signature_image')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Active Checkbox -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $signer->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-slate-300">
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
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function previewImage(input) {
    const previewContainer = document.getElementById('preview-container');
    const preview = document.getElementById('image-preview');
    const currentLabel = document.getElementById('current-label');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
            if (currentLabel) {
                currentLabel.textContent = 'Gambar baru terpilih';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
