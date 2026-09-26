@extends('layouts.app')

@section('title', 'Masuk - E-Belajar Kabupaten Bengkalis')

@push('styles')
    <style>
        footer {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-4 sm:py-6">
        <div class="w-full max-w-[380px] bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-lg space-y-3.5">
            <div class="text-center">
                <img src="{{ asset('storage/logo.png') }}" alt="Logo Kabupaten Bengkalis"
                    class="w-10 h-10 mx-auto object-contain mb-1.5 drop-shadow-xs">
                <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Masuk ke Portal</h1>
                <p class="text-[11px] text-slate-500 mt-0.5">E-Belajar Pemerintah Kabupaten Bengkalis</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label for="nip" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nomor Induk Pegawai (NIP)</label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}" required autofocus placeholder="Masukkan NIP Anda"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                    @error('nip')
                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-[10px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kata
                        Sandi</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs sm:text-sm focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden transition-colors">
                    @error('password')
                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Captcha Row -->
                <div>
                    <div class="flex items-center gap-2">
                        <div
                            class="h-9 w-28 shrink-0 bg-white rounded-xl border border-slate-300 overflow-hidden flex items-center justify-center shadow-xs">
                            <img id="captcha-img" src="{{ route('captcha') }}?t={{ time() }}" alt="Kode Captcha"
                                class="h-full w-full object-cover select-none cursor-pointer" onclick="refreshCaptcha()"
                                title="Klik untuk muat ulang kode">
                        </div>
                        <button type="button" onclick="refreshCaptcha()" title="Muat ulang Captcha"
                            class="h-9 w-9 shrink-0 flex items-center justify-center rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                        </button>
                        <div class="flex-1">
                            <input type="text" name="captcha" id="captcha" placeholder="Kode Captcha" maxlength="6" required
                                autocomplete="off"
                                class="w-full h-9 px-3 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold tracking-wider text-slate-800 placeholder-slate-400 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 focus:outline-hidden">
                        </div>
                    </div>
                    @error('captcha')
                        <span class="text-[10px] text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-[11px]">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded text-emerald-700 focus:ring-emerald-500">
                        <span class="text-slate-600">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors shadow-sm hover:shadow text-xs sm:text-sm">
                    Masuk Sekarang
                </button>
            </form>

            <div class="text-center text-[11px] text-slate-500 pt-1 border-t border-slate-100">
                Belum memiliki akun? <a href="{{ route('register') }}"
                    class="font-bold text-emerald-700 hover:underline">Daftar Akun Baru</a>
            </div>
        </div>
    </div>

    <script>
        function fillCreds(nip, pass) {
            const nipInput = document.getElementById('nip');
            if (nipInput) {
                nipInput.value = nip;
            }
            document.getElementById('password').value = pass;
            const captchaInput = document.getElementById('captcha');
            if (captchaInput) {
                captchaInput.focus();
            }
        }

        function refreshCaptcha() {
            const img = document.getElementById('captcha-img');
            if (img) {
                img.src = "{{ route('captcha') }}?t=" + new Date().getTime();
            }
            const input = document.getElementById('captcha');
            if (input) {
                input.value = '';
                input.focus();
            }
        }
    </script>
@endsection