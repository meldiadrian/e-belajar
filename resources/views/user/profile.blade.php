@extends('layouts.admin')

@section('title', 'Edit Profil & Kata Sandi - E-Belajar Kabupaten Bengkalis')
@section('page_title', 'Edit Profil & Kata Sandi')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header Profile Card -->
        <div class="bg-gradient-to-r from-[#4D52B4] to-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div class="relative shrink-0">
                @if($user->avatar)
                    <img id="headerAvatarImg" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-[#4E9CE8] shadow-md">
                @else
                    <div id="headerAvatarFallback" class="w-20 h-20 rounded-2xl gradient-bengkalis flex items-center justify-center text-white text-2xl font-black shadow-md border-2 border-[#4E9CE8]">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="text-center sm:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <h1 class="text-xl sm:text-2xl font-black text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->isSuperAdmin() ? 'bg-amber-500/30 text-amber-200 border border-amber-400/30' : ($user->isAdmin() ? 'bg-[#4E9CE8]/30 text-[#4E9CE8] border border-[#4E9CE8]/30' : 'bg-slate-500/30 text-slate-200 border border-slate-400/30') }}">
                        {{ $user->isSuperAdmin() ? 'Super Admin' : ($user->isAdmin() ? 'Admin Kursus' : 'Peserta') }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 font-mono">
                    @if($user->nip)
                        <span class="text-[#4E9CE8] font-bold">NIP: {{ $user->nip }}</span> &bull;
                    @endif
                    {{ $user->email }}
                </p>
                <p class="text-xs text-[#4E9CE8] mt-1 font-semibold">{{ $user->institution ?? ($user->isAdmin() ? 'Pemerintah Kabupaten Bengkalis' : 'Masyarakat Umum / Aparatur') }}</p>
            </div>
        </div>

        <!-- Form Update Profile -->
        <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#4D52B4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Informasi Pribadi & Kepegawaian</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        @error('name')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        @error('email')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="nip" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        NIP (Nomor Induk Pegawai)
                        <span class="text-[10px] text-[#4D52B4] lowercase font-normal">(digunakan untuk login)</span>
                    </label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip', $user->nip) }}" placeholder="Contoh: 198501012010011001" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                    @error('nip')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="institution" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instansi / Unit Kerja</label>
                        <input type="text" name="institution" id="institution" value="{{ old('institution', $user->institution) }}" placeholder="Contoh: Disdik Bengkalis / Bappeda" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        @error('institution')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
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
                                <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-[#4E9CE8]/30 shadow-xs">
                            @else
                                <div id="avatarPreviewFallback" class="w-16 h-16 rounded-2xl gradient-bengkalis flex items-center justify-center text-white font-black text-lg shadow-xs ring-2 ring-[#4E9CE8]/30">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" onchange="previewAvatar(this)" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#4E9CE8]/10 file:text-[#4D52B4] hover:file:bg-[#4E9CE8]/20 cursor-pointer">
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
                        <svg class="w-4 h-4 text-[#4D52B4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Ubah Kata Sandi (Password)</span>
                    </h3>
                    <span class="text-[11px] text-slate-400 font-medium">Kosongkan jika tidak ingin mengubah</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru</label>
                        <input type="password" name="password" id="password" autocomplete="new-password" placeholder="Minimal 6 karakter" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                        @error('password')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-[#4E9CE8] focus:outline-hidden">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                    &larr; Batal & Kembali ke Dashboard
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-[#4D52B4] hover:bg-[#4E9CE8] text-white font-bold text-xs shadow-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Google Authenticator (2FA) Security Card -->
    <div id="two-factor-section" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl {{ $user->hasTwoFactorEnabled() ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Autentikasi Dua Faktor (Google Authenticator)</h2>
                    <p class="text-xs text-slate-500">Perlindungan ganda akun dengan kode sandi satu kali (TOTP)</p>
                </div>
            </div>

            <div>
                @if($user->hasTwoFactorEnabled())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>2FA Aktif & Terlindungi</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>2FA Belum Aktif</span>
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            @if($user->hasTwoFactorEnabled())
                <!-- 2FA Active Details -->
                <div class="p-4 sm:p-5 bg-emerald-50/60 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="text-xs sm:text-sm font-bold text-emerald-950 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Akun Anda telah diamankan dengan Google Authenticator</span>
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            Setiap kali Anda masuk ke portal E-Belajar, sistem akan meminta 6-digit kode OTP dari aplikasi authenticator di ponsel Anda.
                        </p>
                        @if($user->two_factor_confirmed_at)
                            <div class="text-[11px] text-emerald-700/80 pt-1 font-mono">
                                Diaktifkan pada: {{ $user->two_factor_confirmed_at->format('d M Y, H:i') }} WIB
                            </div>
                        @endif
                    </div>

                    <!-- Disable Button -->
                    <button type="button" onclick="document.getElementById('modalDisable2FA').classList.remove('hidden')"
                        class="px-4 py-2.5 bg-white hover:bg-red-50 text-red-600 border border-red-200 hover:border-red-300 font-bold text-xs rounded-xl shadow-xs transition-colors shrink-0 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Nonaktifkan 2FA</span>
                    </button>
                </div>

                <!-- Modal Confirm Disable 2FA -->
                <div id="modalDisable2FA" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
                    <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-base font-bold text-slate-900">Konfirmasi Nonaktifkan 2FA</h3>
                            <button type="button" onclick="document.getElementById('modalDisable2FA').classList.add('hidden')"
                                class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
                        </div>
                        <p class="text-xs text-slate-600">
                            Menonaktifkan 2FA akan mengurangi tingkat keamanan akun Anda. Masukkan kata sandi saat ini untuk melanjutkan:
                        </p>
                        <form action="{{ route('profile.2fa.disable', $user->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Saat Ini</label>
                                <input type="password" name="current_password" id="current_password" required placeholder="Masukkan kata sandi akun"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-red-500 focus:outline-hidden">
                                @error('current_password')
                                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" onclick="document.getElementById('modalDisable2FA').classList.add('hidden')"
                                    class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-md">
                                    Ya, Nonaktifkan 2FA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- 2FA Inactive / Setup Section -->
                <div class="space-y-4">
                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl text-xs text-amber-900 leading-relaxed flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-bold">Direkomendasikan untuk hak akses {{ ucfirst($user->role) }}:</span>
                            Aktifkan Google Authenticator untuk mencegah akses tidak sah ke panel e-Belajar meskipun kata sandi Anda diketahui pihak lain.
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200 space-y-5">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <!-- QR Code -->
                            <div class="shrink-0 bg-white p-3 rounded-2xl border border-slate-200 shadow-xs flex flex-col items-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ rawurlencode($qrCodeUri ?? '') }}"
                                    alt="QR Code Google Authenticator" class="w-32 h-32 object-contain">
                                <span class="text-[10px] text-slate-400 mt-1.5 font-mono">Pindai dengan Aplikasi</span>
                            </div>

                            <!-- Secret Key Details -->
                            <div class="flex-1 w-full space-y-3">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Langkah 1: Pindai Kode QR</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Buka aplikasi <span class="font-semibold text-slate-700">Google Authenticator</span> atau <span class="font-semibold text-slate-700">Microsoft Authenticator</span> di HP Anda, pilih tambah akun, lalu pindai kode QR di samping.
                                    </p>
                                </div>

                                <div>
                                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block">Atau Gunakan Kunci Penyiapan Manual (Secret Key):</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <code class="px-3 py-1.5 bg-white rounded-xl text-slate-800 text-xs font-mono font-bold tracking-wider border border-slate-300 select-all shadow-2xs">
                                            {{ $formattedSecret ?? $secret ?? '' }}
                                        </code>
                                        <button type="button" onclick="copy2FASecret('{{ $secret ?? '' }}')" id="btn-copy-2fa"
                                            class="px-2.5 py-1.5 text-xs font-semibold text-[#4D52B4] hover:text-white bg-[#4D52B4]/10 hover:bg-[#4D52B4] rounded-xl transition-colors flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            <span id="copy-2fa-text">Salin</span>
                                        </button>
                                    </div>
                                </div>

                                @if(!empty($qrCodeUri))
                                    <div>
                                        <a href="{{ $qrCodeUri }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#4D52B4] hover:text-[#4E9CE8] hover:underline">
                                            <span>Buka langsung di aplikasi HP</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Verification Step Form -->
                        <form action="{{ route('profile.2fa.enable', $user->id) }}" method="POST" class="pt-4 border-t border-slate-200 space-y-4">
                            @csrf
                            <input type="hidden" name="two_factor_secret" value="{{ $secret ?? '' }}">

                            <div>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Langkah 2: Masukkan Kode Verifikasi OTP</h4>
                                <p class="text-xs text-slate-500 mb-2">
                                    Ketik 6 digit angka yang tampil di aplikasi authenticator untuk memastikan sinkronisasi berhasil:
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                                    <div class="relative w-full sm:w-64">
                                        <input type="text" name="two_factor_code" id="profile_two_factor_code" required maxlength="6" inputmode="numeric"
                                            autocomplete="one-time-code" placeholder="Contoh: 123456"
                                            class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('two_factor_code') ? 'border-red-500 bg-red-50/40 ring-1 ring-red-400' : 'border-slate-300 bg-white' }} text-sm font-semibold tracking-widest text-slate-900 focus:border-[#4E9CE8] focus:outline-hidden">
                                    </div>
                                    <button type="submit"
                                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#4D52B4] hover:bg-[#4E9CE8] text-white font-bold text-xs shadow-md transition-colors flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Verifikasi & Aktifkan 2FA</span>
                                    </button>
                                </div>
                                @error('two_factor_code')
                                    <span class="text-xs text-red-600 mt-1 block font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
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
                container.innerHTML = `<img src="${e.target.result}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-[#4E9CE8] shadow-xs">`;
            }
        };
        reader.readAsDataURL(file);
    }
}

function copy2FASecret(secret) {
    if (!secret) return;
    navigator.clipboard.writeText(secret).then(() => {
        const copyText = document.getElementById('copy-2fa-text');
        if (copyText) {
            const original = copyText.innerText;
            copyText.innerText = 'Tersalin! ✓';
            setTimeout(() => {
                copyText.innerText = original;
            }, 2000);
        }
    }).catch(err => {
        console.error('Gagal menyalin:', err);
    });
}
</script>
@endsection
