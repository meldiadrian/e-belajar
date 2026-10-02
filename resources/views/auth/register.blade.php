@extends('layouts.app')

@section('title', 'Daftar Akun Baru - E-Belajar Kabupaten Bengkalis')

@push('styles')
    <style>
        body {
            background-image: url("{{ asset('images/auth-bg.jpg') }}") !important;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        footer {
            display: none !important;
        }
    </style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-4 sm:py-6">
    <div class="w-full max-w-lg bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-lg space-y-3.5">
        <div class="text-center">
            <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                class="w-10 h-10 mx-auto object-contain mb-1.5 drop-shadow-xs">
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Peserta</h1>
            <p class="text-[11px] text-slate-500 mt-0.5">Lengkapi formulir dan aktifkan autentikator untuk pembelajaran digital</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Mohon periksa data yang Anda masukkan:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-3">
            @csrf

            <!-- Hidden 2FA Secret -->
            <input type="hidden" name="two_factor_secret" value="{{ $secret ?? old('two_factor_secret') }}">

            <div class="space-y-2.5">
                <div>
                    <label for="name" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Lengkap *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus placeholder="Nama lengkap Anda beserta gelar"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                    @error('name')
                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Alamat Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="nama@email.com"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                        @error('email')
                            <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="nip" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                            NIP * <span class="text-[9px] text-[#4D52B4] font-normal lowercase">(untuk login portal)</span>
                        </label>
                        <input type="text" name="nip" id="nip" value="{{ old('nip') }}" required placeholder="Contoh: 198501012010011001"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                        @error('nip')
                            <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="institution" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Instansi / Unit Kerja</label>
                        <input type="text" name="institution" id="institution" value="{{ old('institution') }}" placeholder="Contoh: Disdik Bengkalis"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                    </div>
                    <div>
                        <label for="phone" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor WhatsApp/HP</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kata Sandi *</label>
                        <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                        @error('password')
                            <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Ulangi Kata Sandi *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi kata sandi"
                            class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                    </div>
                </div>
            </div>

            <!-- ================= GOOGLE AUTHENTICATOR SETUP SECTION ================= -->
            <div class="p-3.5 bg-gradient-to-br from-slate-50 to-blue-50/40 rounded-2xl border border-slate-200/90 space-y-3">
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#4D52B4]/10 text-[#4D52B4] flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900">Aktivasi Google Authenticator</h2>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Buka aplikasi <span class="font-semibold text-slate-700">Google Authenticator</span> atau <span class="font-semibold text-slate-700">Microsoft Authenticator</span> di ponsel Anda, lalu pindai QR code atau salin kode kunci rahasia.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3.5 bg-white p-3 rounded-xl border border-slate-200">
                    <!-- QR Code Container -->
                    <div class="shrink-0 bg-white p-1.5 rounded-lg border border-slate-200 shadow-xs flex flex-col items-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ rawurlencode($qrCodeUri ?? '') }}"
                            alt="QR Code Google Authenticator"
                            class="w-28 h-28 object-contain">
                        <span class="text-[9px] text-slate-400 mt-1 font-mono">Pindai QR</span>
                    </div>

                    <!-- Secret Key Details -->
                    <div class="flex-1 w-full space-y-2 text-left">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Kunci Penyiapan Manual (Secret Key)</span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <code id="raw-secret" class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-800 text-xs font-mono font-bold tracking-wider select-all border border-slate-200">
                                    {{ $formattedSecret ?? $secret ?? '' }}
                                </code>
                                <button type="button" onclick="copySecret('{{ $secret ?? '' }}')" id="copy-btn"
                                    class="px-2 py-1 text-[11px] font-semibold text-[#4D52B4] hover:text-white bg-[#4D52B4]/10 hover:bg-[#4D52B4] rounded-lg transition-colors flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <span id="copy-text">Salin</span>
                                </button>
                            </div>
                        </div>

                        @if(!empty($qrCodeUri))
                            <div>
                                <a href="{{ $qrCodeUri }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#4D52B4] hover:text-[#4E9CE8] hover:underline">
                                    <span>Buka langsung di aplikasi HP</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- OTP Verification Input -->
                <div>
                    <label for="two_factor_code" class="block text-[10px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Masukkan 6-Digit Kode Verifikasi OTP *
                    </label>
                    <input type="text" name="two_factor_code" id="two_factor_code" required maxlength="6" inputmode="numeric"
                        autocomplete="one-time-code" placeholder="Contoh: 123456"
                        class="w-full px-3.5 py-2 rounded-xl border {{ $errors->has('two_factor_code') ? 'border-red-500 bg-red-50/40 ring-1 ring-red-400' : 'border-slate-300' }} text-xs sm:text-sm font-semibold tracking-widest text-slate-900 focus:border-[#4E9CE8] focus:ring-1 focus:ring-[#4E9CE8] focus:outline-hidden transition-colors">
                    <p class="text-[10px] text-slate-500 mt-1">
                        Ketik 6 digit angka yang tampil di aplikasi authenticator untuk memverifikasi pemasangan.
                    </p>
                    @error('two_factor_code')
                        <span class="text-[10px] text-red-600 mt-1 block font-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit"
                class="w-full py-2.5 rounded-xl font-bold text-white bg-[#4D52B4] hover:bg-[#4E9CE8] transition-colors shadow-sm hover:shadow text-xs sm:text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Daftar & Aktifkan Autentikator</span>
            </button>
        </form>

        <div class="text-center text-[11px] text-slate-500 pt-1 border-t border-slate-100">
            Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-[#4D52B4] hover:underline">Masuk ke Portal</a>
        </div>
    </div>
</div>

<script>
    function copySecret(secret) {
        if (!secret) return;
        navigator.clipboard.writeText(secret).then(() => {
            const copyText = document.getElementById('copy-text');
            const original = copyText.innerText;
            copyText.innerText = 'Tersalin! ✓';
            setTimeout(() => {
                copyText.innerText = original;
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }
</script>
@endsection
