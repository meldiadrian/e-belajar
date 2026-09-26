@extends(Auth::user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Edit Profil & Kata Sandi - E-Belajar Kabupaten Bengkalis')
@section('page_title', 'Edit Profil & Kata Sandi')

@section('content')
<div class="{{ Auth::user()->isAdmin() ? 'max-w-4xl space-y-6' : 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10' }}">
    @if(!Auth::user()->isAdmin())
        <!-- Breadcrumb untuk Pengguna Publik -->
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-emerald-700">Beranda</a>
            <span>/</span>
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700">Dashboard</a>
            <span>/</span>
            <span class="font-bold text-slate-800">Edit Profil</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Profile Card -->
        <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="relative shrink-0">
                @if($user->avatar)
                    <img id="headerAvatarImg" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-400 shadow-md">
                @else
                    <div id="headerAvatarFallback" class="w-20 h-20 rounded-2xl gradient-bengkalis flex items-center justify-center text-white text-2xl font-black shadow-md border-2 border-emerald-400">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="text-center sm:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <h1 class="text-xl sm:text-2xl font-black text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->isSuperAdmin() ? 'bg-amber-500/30 text-amber-200 border border-amber-400/30' : ($user->isAdmin() ? 'bg-emerald-500/30 text-emerald-200 border border-emerald-400/30' : 'bg-slate-500/30 text-slate-200 border border-slate-400/30') }}">
                        {{ $user->isSuperAdmin() ? 'Super Admin' : ($user->isAdmin() ? 'Admin Kursus' : 'Peserta') }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 font-mono">
                    @if($user->nip)
                        <span class="text-emerald-300 font-bold">NIP: {{ $user->nip }}</span> &bull;
                    @endif
                    {{ $user->email }}
                </p>
                <p class="text-xs text-emerald-300 mt-1 font-semibold">{{ $user->institution ?? ($user->isAdmin() ? 'Pemerintah Kabupaten Bengkalis' : 'Masyarakat Umum / Aparatur') }}</p>
            </div>
        </div>

        <!-- Form Update Profile -->
        <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Informasi Pribadi & Kepegawaian</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('name')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('email')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            NIP (Nomor Induk Pegawai)
                            <span class="text-[10px] text-emerald-600 lowercase font-normal">(digunakan untuk login)</span>
                        </label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip', $user->nip) }}" placeholder="Contoh: 198501012010011001" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('nip')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik', $user->nik) }}" placeholder="Contoh: 1403xxxxxxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('nik')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tempat_lahir" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}" placeholder="Contoh: Bengkalis" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('tempat_lahir')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="agama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Agama</label>
                        <select name="agama" id="agama" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm bg-white focus:border-emerald-600 focus:outline-hidden">
                            <option value="">-- Pilih Agama --</option>
                            @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya'] as $agm)
                                <option value="{{ $agm }}" {{ old('agama', $user->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                            @endforeach
                        </select>
                        @error('agama')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="institution" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instansi / Unit Kerja</label>
                        <input type="text" name="institution" id="institution" value="{{ old('institution', $user->institution) }}" placeholder="Contoh: Disdik Bengkalis / Bappeda" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('institution')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('phone')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="avatar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Foto Profil (Avatar)</label>
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <div id="avatarPreviewContainer" class="shrink-0">
                            @if($user->avatar)
                                <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-emerald-500/30 shadow-xs">
                            @else
                                <div id="avatarPreviewFallback" class="w-16 h-16 rounded-2xl gradient-bengkalis flex items-center justify-center text-white font-black text-lg shadow-xs ring-2 ring-emerald-500/30">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" onchange="previewAvatar(this)" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1.5">Format file: JPG, JPEG, PNG, WEBP (Maksimal 2MB).</p>
                        </div>
                    </div>
                    @error('avatar')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password Change Section -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Ubah Kata Sandi (Password)</span>
                    </h3>
                    <span class="text-[11px] text-slate-400 font-medium">Kosongkan jika tidak ingin mengubah</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" id="password" autocomplete="new-password" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                        @error('password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-emerald-600 focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                    &larr; Batal & Kembali ke Dashboard
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran berkas maksimal 2MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('avatarPreviewContainer');
            if (container) {
                container.innerHTML = `<img src="${e.target.result}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-emerald-500 shadow-xs">`;
            }
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
